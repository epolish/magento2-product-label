<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Ui\Component\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as BaseClass;

/**
 * Class SearchResult
 */
class SearchResult extends BaseClass
{
    /**
     * @param array|string $field
     * @param null $condition
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field != 'store_ids' || !array_key_exists('in', $condition)) {
            return parent::addFieldToFilter($field, $condition);
        }

        return parent::addFieldToFilter($field, array_map(function ($value) {
            return ['finset' => $value];
        }, $condition['in']));
    }
}
