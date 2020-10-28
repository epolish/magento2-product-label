<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ProductLabel
 */
class ProductLabel extends AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('product_label', 'entity_id');
    }
}
