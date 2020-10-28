<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Polish\ProductLabel\Api\ProductLabelManagerInterface;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class Duplicate
 */
class Duplicate extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Polish_ProductLabel::edit';

    /**
     * @var ProductLabelManagerInterface
     */
    private $productLabelManager;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * Duplicate constructor.
     * @param Context $context
     * @param ProductLabelManagerInterface $productLabelManager
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        Context $context,
        ProductLabelManagerInterface $productLabelManager,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        parent::__construct($context);

        $this->productLabelManager = $productLabelManager;
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        try {
            $this->productLabelManager->duplicate(
                $this->productLabelRepository->getById((int)$this->getRequest()->getParam('id'))
            );

            $this->messageManager->addSuccessMessage(__('Product label has been duplicated successfully'));
        } catch (NotFoundException $ex) {
            $this->messageManager->addErrorMessage(__('Product label does not exist'));
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage(__($ex->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
