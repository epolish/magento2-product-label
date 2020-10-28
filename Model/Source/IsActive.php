<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Inactive'), 'value' => 0],
            ['label' => __('Active'), 'value' => 1]
        ];
    }
}
