<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;

/**
 * Interface ProductLabelManagerInterface
 */
interface ProductLabelManagerInterface
{
    /**
     * @param ProductLabelInterface $productLabel
     * @return ProductLabelInterface
     * @throws CouldNotSaveException
     */
    public function duplicate(ProductLabelInterface $productLabel): ProductLabelInterface;
}
