<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model\ResourceModel\ProductLabel;

use Polish\ProductLabel\Model\ProductLabel as Model;
use Polish\ProductLabel\Model\ResourceModel\ProductLabel as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'product_label_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'product_label_collection';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
