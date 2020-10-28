<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Polish\ProductLabel\Api\Data\ProductLabelSearchResultsInterface;

/**
 * Interface ProductLabelListInterface
 */
interface ProductLabelListInterface
{
    const ALL_STORE_VIEWS_FILTER_VALUE = 0;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductLabelSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ProductLabelSearchResultsInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param array $ids
     * @return ProductLabelListInterface
     */
    public function addIdsFilter(SearchCriteriaInterface $searchCriteria, array $ids): ProductLabelListInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $isActive
     * @return ProductLabelListInterface
     */
    public function addActiveFilter(
        SearchCriteriaInterface $searchCriteria,
        bool $isActive = true
    ): ProductLabelListInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $storeId
     * @return ProductLabelListInterface
     */
    public function addStoreIdFilter(
        SearchCriteriaInterface $searchCriteria,
        int $storeId = self::ALL_STORE_VIEWS_FILTER_VALUE
    ): ProductLabelListInterface;
}
