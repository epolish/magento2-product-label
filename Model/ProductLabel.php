<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model;

use Magento\CatalogRule\Api\Data\RuleInterface as CatalogRuleInterface;
use Magento\CatalogRule\Api\Data\RuleInterfaceFactory as CatalogRuleInterfaceFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\StoreManagerInterface;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Model\ResourceModel\ProductLabel as ResourceModel;

/**
 * Class ProductLabel
 */
class ProductLabel extends AbstractModel implements IdentityInterface, ProductLabelInterface
{
    use ProductLabelProperties;

    const CACHE_TAG = 'product_label_model';

    /**
     * @var string
     */
    protected $_cacheTag = 'product_label_model';

    /**
     * @var string
     */
    protected $_eventPrefix = 'product_label_model';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var CatalogRuleInterfaceFactory
     */
    private $catalogRuleFactory;

    /**
     * ProductLabel constructor.
     * @param Context $context
     * @param Registry $registry
     * @param JsonSerializer $jsonSerializer
     * @param StoreManagerInterface $storeManager
     * @param CatalogRuleInterfaceFactory $catalogRuleFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        JsonSerializer $jsonSerializer,
        StoreManagerInterface $storeManager,
        CatalogRuleInterfaceFactory $catalogRuleFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->jsonSerializer = $jsonSerializer;
        $this->catalogRuleFactory = $catalogRuleFactory;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }

    /**
     * @return int[]
     */
    public function getStoreIds(): array
    {
        $localCacheName = '_' . ProductLabelInterface::STORE_IDS;

        if ($this->hasDataChanges() || !$this->hasData($localCacheName)) {
            $storeIds = $this->getData(ProductLabelInterface::STORE_IDS);

            if (is_string($storeIds)) {
                $storeIds = explode(',', $this->getData(ProductLabelInterface::STORE_IDS));
            }

            $this->setData($localCacheName, array_map('intval', $storeIds));
        }

        return $this->getData($localCacheName);
    }

    /**
     * @param array $storeIds
     * @return ProductLabelInterface
     */
    public function setStoreIds(array $storeIds): ProductLabelInterface
    {
        return $this->setData(
            ProductLabelInterface::STORE_IDS,
            implode(',', array_map('intval', $storeIds))
        );
    }

    /**
     * @param CatalogRuleInterface $catalogRule
     * @return ProductLabelInterface
     */
    public function setCatalogRule(CatalogRuleInterface $catalogRule): ProductLabelInterface
    {
        return $this->setData('catalog_rule', $catalogRule);
    }

    /**
     * @return CatalogRuleInterface
     */
    public function getCatalogRule(): CatalogRuleInterface
    {
        if (!$this->getData('catalog_rule') instanceof CatalogRuleInterface) {
            $websiteIds = array_values(array_map(function ($website) {
                return (int)$website->getId();
            }, $this->storeManager->getWebsites(true)));

            $this->setCatalogRule(
                $this->catalogRuleFactory->create()
                    ->setWebsiteIds([$websiteIds])
                    ->setConditionsSerialized($this->getData('conditions_serialized'))
            );
        }

        return $this->getData('catalog_rule');
    }

    /**
     * @return ProductLabelInterface
     * @throws StateException
     */
    public function beforeSave()
    {
        if (!$this->getCatalogRule() instanceof CatalogRuleInterface) {
            throw new StateException(__('Catalog rule is not set'));
        }

        $this->setData(
            'conditions_serialized',
            $this->jsonSerializer->serialize($this->getCatalogRule()->getConditions()->asArray())
        );

        return parent::beforeSave();
    }
}
