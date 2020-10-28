<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model;

use DateTime;
use Magento\Framework\Exception\CouldNotSaveException;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Api\ProductLabelManagerInterface;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class ProductLabelManager
 */
class ProductLabelManager implements ProductLabelManagerInterface
{
    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * ProductLabelManager constructor.
     * @param ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @param ProductLabelInterface $productLabel
     * @return ProductLabelInterface
     * @throws CouldNotSaveException
     */
    public function duplicate(ProductLabelInterface $productLabel): ProductLabelInterface
    {
        $productLabel->setEntityId(null);
        $productLabel->setTitle($productLabel->getTitle() . '_Copy');
        $productLabel->setImage('');
        $productLabel->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));
        $productLabel->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));
        $productLabel->setIsActive(false);
        $productLabel->setDescription($productLabel->getDescription());
        $productLabel->setStoreIds($productLabel->getStoreIds());
        $productLabel->setCatalogRule($productLabel->getCatalogRule());

        $this->productLabelRepository->save($productLabel);

        return $productLabel;
    }
}
