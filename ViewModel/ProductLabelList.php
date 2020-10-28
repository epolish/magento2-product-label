<?php
declare(strict_types=1);

namespace Polish\ProductLabel\ViewModel;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Api\ProductLabelListInterface;
use Polish\ProductLabel\Model\ProductLabel\ImageUrl as ProductLabelImageUrl;

/**
 * Class ProductLabelList
 */
class ProductLabelList implements ArgumentInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductLabelListInterface
     */
    private $productLabelList;

    /**
     * @var ProductLabelImageUrl
     */
    private $productLabelImageUrl;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * ProductLabelList constructor.
     * @param Escaper $escaper
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ProductLabelImageUrl $productLabelImageUrl
     * @param ProductLabelListInterface $productLabelList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Escaper $escaper,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ProductLabelImageUrl $productLabelImageUrl,
        ProductLabelListInterface $productLabelList,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->escaper = $escaper;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->productLabelList = $productLabelList;
        $this->productLabelImageUrl = $productLabelImageUrl;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return Escaper
     */
    public function getEscaper()
    {
        return $this->escaper;
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return string
     */
    public function getProductLabelImageUrl(ProductLabelInterface $productLabel): string
    {
        try {
            return $this->productLabelImageUrl->getProductLabelImageUrl($productLabel->getImage());
        } catch (NoSuchEntityException $ex) {
            return '';
        }
    }

    /**
     * @return ProductLabelInterface[]
     * @throws NoSuchEntityException
     */
    public function getProductLabels()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();

        return array_filter(
            $this->productLabelList->addActiveFilter($searchCriteria, true)
                ->addStoreIdFilter($searchCriteria, (int)$this->storeManager->getStore()->getId())
                ->getList($searchCriteria)
                ->getItems(),
            function ($productLabel) {
                return $productLabel->getCatalogRule()
                                    ->validate($this->registry->registry('product'));
            }
        );
    }
}
