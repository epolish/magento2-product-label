<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Polish\ProductLabel\Api\ProductLabelListInterface;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class MassDelete
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Polish_ProductLabel::edit';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ProductLabelListInterface
     */
    private $productLabelList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * MassDuplicate constructor.
     * @param Context $context
     * @param Filter $filter
     * @param ProductLabelListInterface $productLabelList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ProductLabelListInterface $productLabelList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        $this->filter = $filter;
        $this->productLabelList = $productLabelList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productLabelRepository = $productLabelRepository;

        parent::__construct($context);
    }

    /**
     * @return Redirect
     * @throws Exception
     * @throws LocalizedException
     */
    public function execute()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $productLabels = $this->productLabelList->addIdsFilter($searchCriteria, $this->getRequestIds())
            ->getList($searchCriteria)
            ->getItems();

        foreach ($productLabels as $productLabel) {
            $this->productLabelRepository->delete($productLabel);
        }

        $this->messageManager->addSuccessMessage(__(
            'A total of %1 record(s) have been deleted.',
            count($productLabels)
        ));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return array|int[]
     * @throws LocalizedException
     */
    private function getRequestIds()
    {
        $this->filter->applySelectionOnTargetProvider();

        return array_map(
            function ($item) {
                return (int)$item->getId();
            },
            $this->filter->getComponent()
                ->getContext()
                ->getDataProvider()
                ->getSearchResult()
                ->getItems()
        );
    }
}
