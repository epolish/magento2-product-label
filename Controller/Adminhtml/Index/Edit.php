<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class Edit
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Polish_ProductLabel::edit';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * Product Label edit page
     *
     * @return Page|ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Polish_ProductLabel::list');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Label Edit'));

        try {
            if ((int)$this->getRequest()->getParam('id')) {
                $this->productLabelRepository->getById((int)$this->getRequest()->getParam('id'));
            }

            return $resultPage;
        } catch (NotFoundException $ex) {
            $this->messageManager->addErrorMessage($ex->getMessage());

            return $this->resultRedirectFactory->create()->setPath('*/*');
        }
    }
}
