<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Exception\NotFoundException;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class Validate
 */
class Validate extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Polish_ProductLabel::edit';

    /**
     * @var ResultJsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * Validate constructor.
     * @param Context $context
     * @param ResultJsonFactory $resultJsonFactory
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        Context $context,
        ResultJsonFactory $resultJsonFactory,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @return ResultJson
     */
    public function execute()
    {
        $validationResult = $this->validate();

        return $this->resultJsonFactory->create()->setData([
            'messages' => $validationResult,
            'error' => (bool)count($validationResult)
        ]);
    }

    /**
     * @return string[]
     */
    private function validate()
    {
        $errorMessages = [];

        if ((int)$this->getRequest()->getPostValue('entity_id')) {
            try {
                $this->productLabelRepository->getById((int)$this->getRequest()->getPostValue('entity_id'));
            } catch (NotFoundException $ex) {
                $errorMessages[] = __('Product label does not exist');
            }
        }

        if (!is_array($this->getRequest()->getPostValue('image'))) {
            $errorMessages[] =__('"Image" field is required');
        }

        if (!is_array($this->getRequest()->getPostValue('store_ids'))) {
            $errorMessages[] =__('"Store View" field is required');
        }

        if (!(string)$this->getRequest()->getPostValue('title')) {
            $errorMessages[] = __('"Title" field is required');
        }

        return $errorMessages;
    }
}
