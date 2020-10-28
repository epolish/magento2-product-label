<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\CatalogRule\Api\Data\RuleInterfaceFactory as CatalogRuleInterfaceFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Api\Data\ProductLabelInterfaceFactory;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;
use Polish\ProductLabel\Model\ProductLabel\ImageUploader;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Polish_ProductLabel::edit';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var ResultJsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CatalogRuleInterfaceFactory
     */
    private $catalogRuleFactory;

    /**
     * @var ProductLabelInterfaceFactory
     */
    private $productLabelFactory;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ImageUploader $imageUploader
     * @param ResultJsonFactory $resultJsonFactory
     * @param CatalogRuleInterfaceFactory $catalogRuleFactory
     * @param ProductLabelInterfaceFactory $productLabelFactory
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ImageUploader $imageUploader,
        ResultJsonFactory $resultJsonFactory,
        CatalogRuleInterfaceFactory $catalogRuleFactory,
        ProductLabelInterfaceFactory $productLabelFactory,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->imageUploader = $imageUploader;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->catalogRuleFactory = $catalogRuleFactory;
        $this->productLabelFactory = $productLabelFactory;
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @return ResultJson
     */
    public function execute()
    {
        try {
            $this->save();

            $this->messageManager->addSuccessMessage(__('Product label has been saved successfully'));
        } catch (NotFoundException $ex) {
            $this->messageManager->addErrorMessage(__('Product label does not exist'));
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage(__($ex->getMessage()));
        }

        return $this->resultJsonFactory->create()->setData([
            'ajaxExpired' => 1,
            'ajaxRedirect' => $this->getUrl('*/index')
        ]);
    }

    /**
     * @return ProductLabelInterface
     * @throws NotFoundException
     * @throws LocalizedException
     */
    private function save()
    {
        $productLabel = $this->loadProductLabel()
            ->setTitle((string)$this->getRequest()->getPostValue('title'))
            ->setIsActive((bool)$this->getRequest()->getPostValue('is_active'))
            ->setDescription((string)$this->getRequest()->getPostValue('description'))
            ->setStoreIds(array_map('intval', $this->getRequest()->getPostValue('store_ids')));

        $this->saveImageOnProductLabel($productLabel);
        $this->saveCatalogRuleOnProductLabel($productLabel);

        $this->productLabelRepository->save($productLabel);

        return $productLabel;
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return ProductLabelInterface
     */
    private function saveCatalogRuleOnProductLabel(ProductLabelInterface $productLabel)
    {
        if (array_key_exists('conditions', ($this->getRequest()->getPostValue('rule') ?: []))) {
            $productLabel->setCatalogRule(
                $this->catalogRuleFactory->create()
                    ->loadPost(['conditions' => $this->getRequest()->getPostValue('rule')['conditions']])
            );
        }

        return $productLabel;
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return ProductLabelInterface
     * @throws LocalizedException
     */
    private function saveImageOnProductLabel(ProductLabelInterface $productLabel)
    {
        if (!$this->checkImagePostValue()) {
            throw new LocalizedException(__('Invalid image data was supplied'));
        }

        try {
            if (!$this->isNewImageUploaded($productLabel)) {
                return $productLabel;
            }

            $productLabel->setImage(
                $this->imageUploader->saveProductLabelTmpImage(
                    (string)$this->getRequest()->getPostValue('image')[0]['name']
                )
            );
        } catch (Exception $ex) {
            $this->logException($ex);

            throw new LocalizedException(__(
                'Something went wrong during saving the image. Try to upload the image again.'
            ));
        }

        return $productLabel;
    }

    /**
     * @return bool
     */
    private function checkImagePostValue(): bool
    {
        return is_array($this->getRequest()->getPostValue('image')) &&
               isset($this->getRequest()->getPostValue('image')[0]) &&
               is_array($this->getRequest()->getPostValue('image')[0]) &&
               array_key_exists('name', $this->getRequest()->getPostValue('image')[0]);
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return bool
     */
    private function isNewImageUploaded(ProductLabelInterface $productLabel): bool
    {
        $oldImageName = $this->imageUploader->getImageNameFromRelativePath($productLabel->getImage());
        $newImageName = (string)$this->getRequest()->getPostValue('image')[0]['name'];

        return $oldImageName !== $newImageName;
    }

    /**
     * @param Exception $ex
     */
    private function logException(Exception $ex)
    {
        $this->logger->error(sprintf(
            "Message: %s; File: %s; Line: %s; Param: %s;",
            $ex->getMessage(),
            $ex->getFile(),
            $ex->getLine(),
            (string)$this->getRequest()->getPostValue('image')[0]['name']
        ));
    }

    /**
     * @return ProductLabelInterface
     * @throws NotFoundException
     */
    private function loadProductLabel()
    {
        if ((int)$this->getRequest()->getPostValue('entity_id')) {
            $productLabel = $this->productLabelRepository->getById(
                (int)$this->getRequest()->getPostValue('entity_id')
            );
        } else {
            $productLabel = $this->productLabelFactory->create();
        }

        return $productLabel;
    }
}
