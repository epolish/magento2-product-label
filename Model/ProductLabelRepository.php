<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Api\Data\ProductLabelInterfaceFactory;
use Polish\ProductLabel\Api\Data\ProductLabelSearchResultsInterface;
use Polish\ProductLabel\Api\Data\ProductLabelSearchResultsInterfaceFactory;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;
use Polish\ProductLabel\Model\ResourceModel\ProductLabel as ProductLabelResource;
use Polish\ProductLabel\Model\ResourceModel\ProductLabel\CollectionFactory as ProductLabelCollectionFactory;

/**
 * Class ProductLabelRepository
 */
class ProductLabelRepository implements ProductLabelRepositoryInterface
{
    /**
     * @var array
     */
    private $productLabelCache;

    /**
     * @var ProductLabelInterfaceFactory
     */
    private $productLabelFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ProductLabelResource
     */
    private $productLabelResource;

    /**
     * @var ProductLabelSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ProductLabelCollectionFactory
     */
    private $productLabelCollectionFactory;

    /**
     * ProductLabelRepository constructor.
     * @param ProductLabelResource $productLabelResource
     * @param ProductLabelInterfaceFactory $productLabelFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ProductLabelCollectionFactory $productLabelCollectionFactory
     * @param ProductLabelSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ProductLabelResource $productLabelResource,
        ProductLabelInterfaceFactory $productLabelFactory,
        CollectionProcessorInterface $collectionProcessor,
        ProductLabelCollectionFactory $productLabelCollectionFactory,
        ProductLabelSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->productLabelFactory = $productLabelFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->productLabelResource = $productLabelResource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->productLabelCollectionFactory = $productLabelCollectionFactory;

        $this->initialize();
    }

    /**
     * @param int $id
     * @return ProductLabelInterface
     * @throws NotFoundException
     */
    public function getById(int $id): ProductLabelInterface
    {
        if ($this->hasInCacheById($id)) {
            return $this->getFromCacheById($id);
        }

        return $this->addToCacheById($id, $this->getActualById($id));
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductLabelSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ProductLabelSearchResultsInterface
    {
        $searchResult = $this->searchResultsFactory->create();
        $collection = $this->productLabelCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @param int $id
     * @return ProductLabelInterface
     * @throws NotFoundException
     */
    public function getActualById(int $id): ProductLabelInterface
    {
        $productLabel = $this->productLabelFactory->create();

        $this->productLabelResource->load($productLabel, $id);

        if (!$productLabel || !$productLabel->getId()) {
            throw new NotFoundException(__('Product label does not exist'));
        }

        return $productLabel;
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return bool
     * @throws CouldNotSaveException
     */
    public function save(ProductLabelInterface $productLabel): bool
    {
        try {
            $this->productLabelResource->save($productLabel);

            if ($this->hasInCacheById((int)$productLabel->getEntityId())) {
                $this->purgeFromCacheById((int)$productLabel->getEntityId());
            }
        } catch (Exception $ex) {
            throw new CouldNotSaveException(
                __('The product label was unable to be saved. Please try again.'),
                $ex
            );
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotSaveException
     * @throws NotFoundException
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(ProductLabelInterface $productLabel): bool
    {
        try {
            $this->productLabelResource->delete($productLabel);

            if ($this->hasInCacheById((int)$productLabel->getEntityId())) {
                $this->purgeFromCacheById((int)$productLabel->getEntityId());
            }
        } catch (Exception $ex) {
            throw new CouldNotSaveException(
                __('The product label was unable to be deleted. Please try again.'),
                $ex
            );
        }

        return true;
    }

    /**
     *
     */
    private function initialize()
    {
        $this->productLabelCache = [];
    }

    /**
     * @param int $id
     * @return bool
     */
    private function hasInCacheById(int $id)
    {
        return array_key_exists($id, $this->productLabelCache);
    }

    /**
     * @param int $id
     * @return ProductLabelInterface
     */
    private function getFromCacheById(int $id)
    {
        return $this->productLabelCache[$id];
    }

    /**
     * @param int $id
     * @param ProductLabelInterface $productLabel
     * @return ProductLabelInterface
     */
    private function addToCacheById(int $id, ProductLabelInterface $productLabel)
    {
        $this->productLabelCache[$id] = $productLabel;

        return $this->getFromCacheById($id);
    }

    /**
     * @param int $id
     */
    private function purgeFromCacheById(int $id)
    {
        unset($this->productLabelCache[$id]);
    }
}
