<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Api\Data;

use Magento\CatalogRule\Api\Data\RuleInterface as CatalogRuleInterface;

/**
 * Interface ProductLabelInterface
 */
interface ProductLabelInterface
{
    const ENTITY_ID = 'entity_id';

    const TITLE = 'title';

    const DESCRIPTION = 'description';

    const IS_ACTIVE = 'is_active';

    const STORE_IDS = 'store_ids';

    const IMAGE = 'image';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return ProductLabelInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     * @return ProductLabelInterface
     */
    public function setTitle(string $title): ProductLabelInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return ProductLabelInterface
     */
    public function setDescription(string $description): ProductLabelInterface;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     * @return ProductLabelInterface
     */
    public function setIsActive(bool $isActive): ProductLabelInterface;

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param int[] $storeIds
     * @return ProductLabelInterface
     */
    public function setStoreIds(array $storeIds): ProductLabelInterface;

    /**
     * @return string
     */
    public function getImage(): string;

    /**
     * @param string $image
     * @return ProductLabelInterface
     */
    public function setImage(string $image): ProductLabelInterface;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return ProductLabelInterface
     */
    public function setCreatedAt(string $createdAt): ProductLabelInterface;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     * @return ProductLabelInterface
     */
    public function setUpdatedAt(string $updatedAt): ProductLabelInterface;

    /**
     * @return CatalogRuleInterface
     */
    public function getCatalogRule(): CatalogRuleInterface;

    /**
     * @param CatalogRuleInterface $catalogRule
     * @return ProductLabelInterface
     */
    public function setCatalogRule(CatalogRuleInterface $catalogRule): ProductLabelInterface;
}
