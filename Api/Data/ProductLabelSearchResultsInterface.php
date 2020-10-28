<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ProductLabelSearchResultsInterface
 */
interface ProductLabelSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return ProductLabelInterface[]
     */
    public function getItems();

    /**
     * @param ProductLabelInterface[] $items
     * @return ProductLabelSearchResultsInterface
     */
    public function setItems(array $items);
}
