<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;
use Polish\ProductLabel\Ui\Component\Listing\Columns\StoreOptions as StoreOptionsBase;

/**
 * Class StoreOptionsOptGroup
 */
class StoreOptionsOptGroup implements OptionSourceInterface
{
    /**
     * @var StoreOptions
     */
    private $storeOptionsBase;

    /**
     * StoreOptionsOptGroup constructor.
     * @param StoreOptions $storeOptionsBase
     */
    public function __construct(
        StoreOptionsBase $storeOptionsBase
    ) {
        $this->storeOptionsBase = $storeOptionsBase;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->processOptionsOptGroup($this->storeOptionsBase->toOptionArray());
    }

    /**
     * @param array $options
     * @return array
     */
    private function processOptionsOptGroup(array $options): array
    {
        if (!array_key_exists('value', $options)) {
            $options = array_map([$this, 'processOptionsOptGroup'], $options);
        } elseif (is_array($options['value'])) {
            $options['optgroup'] = $this->processOptionsOptGroup($options['value']);
        }

        return $options;
    }
}
