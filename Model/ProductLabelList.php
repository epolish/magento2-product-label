<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Api\Data\ProductLabelSearchResultsInterface;
use Polish\ProductLabel\Api\ProductLabelListInterface;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class ProductLabelList
 */
class ProductLabelList implements ProductLabelListInterface
{
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * ProductLabelList constructor.
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductLabelSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ProductLabelSearchResultsInterface
    {
        return $this->productLabelRepository->getList($searchCriteria);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param array $ids
     * @return ProductLabelListInterface
     */
    public function addIdsFilter(SearchCriteriaInterface $searchCriteria, array $ids): ProductLabelListInterface
    {
        $filterGroups = $searchCriteria->getFilterGroups();

        $filterGroups[] = $this->filterGroupBuilder->setFilters([
            $this->filterBuilder
                ->setField(ProductLabelInterface::ENTITY_ID)
                ->setConditionType('in')
                ->setValue(array_map('intval', $ids))
                ->create()
        ])->create();

        $searchCriteria->setFilterGroups($filterGroups);

        return $this;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $isActive
     * @return ProductLabelListInterface
     */
    public function addActiveFilter(
        SearchCriteriaInterface $searchCriteria,
        bool $isActive = true
    ): ProductLabelListInterface {
        $filterGroups = $searchCriteria->getFilterGroups();

        $filterGroups[] = $this->filterGroupBuilder->setFilters([
            $this->filterBuilder
                ->setField(ProductLabelInterface::IS_ACTIVE)
                ->setConditionType('eq')
                ->setValue($isActive)
                ->create()
        ])->create();

        $searchCriteria->setFilterGroups($filterGroups);

        return $this;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $storeId
     * @return ProductLabelListInterface
     */
    public function addStoreIdFilter(
        SearchCriteriaInterface $searchCriteria,
        int $storeId = self::ALL_STORE_VIEWS_FILTER_VALUE
    ): ProductLabelListInterface {
        $filters = [];
        $filterGroups = $searchCriteria->getFilterGroups();

        $filters[] = $this->filterBuilder
            ->setField(ProductLabelInterface::STORE_IDS)
            ->setConditionType('finset')
            ->setValue((string)$storeId)
            ->create();

        if ((string)$storeId !== (string)self::ALL_STORE_VIEWS_FILTER_VALUE) {
            $filters[] = $this->filterBuilder
                ->setField(ProductLabelInterface::STORE_IDS)
                ->setConditionType('finset')
                ->setValue((string)self::ALL_STORE_VIEWS_FILTER_VALUE)
                ->create();
        }

        $filterGroups[] = $this->filterGroupBuilder->setFilters($filters)->create();

        $searchCriteria->setFilterGroups($filterGroups);

        return $this;
    }
}
