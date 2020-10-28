<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Ui\Component\Listing\Columns;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as BaseClass;
use Polish\ProductLabel\Api\ProductLabelListInterface;

/**
 * Class StoreOptions
 */
class StoreOptions extends BaseClass
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $defaultValue = (string)ProductLabelListInterface::ALL_STORE_VIEWS_FILTER_VALUE;

        $this->currentOptions['All Store Views']['label'] = __('All Store Views');
        $this->currentOptions['All Store Views']['value'] = $defaultValue;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
