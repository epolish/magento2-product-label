<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Api\Data\ProductLabelSearchResultsInterface;

/**
 * Interface ProductLabelRepositoryInterface
 */
interface ProductLabelRepositoryInterface
{
    /**
     * @param int $id
     * @return ProductLabelInterface
     * @throws NotFoundException
     */
    public function getById(int $id): ProductLabelInterface;
    /**
     * Get product list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductLabelSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ProductLabelSearchResultsInterface;

    /**
     * @param int $id
     * @return ProductLabelInterface
     * @throws NotFoundException
     */
    public function getActualById(int $id): ProductLabelInterface;

    /**
     * @param ProductLabelInterface $productLabel
     * @return bool
     * @throws CouldNotSaveException
     */
    public function save(ProductLabelInterface $productLabel): bool;

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotSaveException
     * @throws NotFoundException
     */
    public function deleteById(int $id): bool;

    /**
     * @param ProductLabelInterface $productLabel
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(ProductLabelInterface $productLabel): bool;
}
