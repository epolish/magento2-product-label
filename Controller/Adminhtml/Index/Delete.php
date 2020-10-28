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
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class Delete
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Polish_ProductLabel::edit';

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        Context $context,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        parent::__construct($context);

        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        try {
            $this->productLabelRepository->deleteById((int)$this->getRequest()->getParam('id'));

            $this->messageManager->addSuccessMessage(__('Product label has been deleted successfully'));
        } catch (NotFoundException $ex) {
            $this->messageManager->addErrorMessage(__('Product label does not exist'));
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage(__($ex->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
