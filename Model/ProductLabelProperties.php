<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model;

use Polish\ProductLabel\Api\Data\ProductLabelInterface;

/**
 * Trait ProductLabelProperties
 */
trait ProductLabelProperties
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData(ProductLabelInterface::TITLE) ?: '';
    }

    /**
     * @param string $title
     * @return ProductLabelInterface
     */
    public function setTitle(string $title): ProductLabelInterface
    {
        return $this->setData(ProductLabelInterface::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getData(ProductLabelInterface::DESCRIPTION) ?: '';
    }

    /**
     * @param string $description
     * @return ProductLabelInterface
     */
    public function setDescription(string $description): ProductLabelInterface
    {
        return $this->setData(ProductLabelInterface::DESCRIPTION, $description);
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData(ProductLabelInterface::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     * @return ProductLabelInterface
     */
    public function setIsActive(bool $isActive): ProductLabelInterface
    {
        return $this->setData(ProductLabelInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->getData(ProductLabelInterface::IMAGE) ?: '';
    }

    /**
     * @param string $image
     * @return ProductLabelInterface
     */
    public function setImage(string $image): ProductLabelInterface
    {
        return $this->setData(ProductLabelInterface::IMAGE, $image);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData(ProductLabelInterface::CREATED_AT) ?: '';
    }

    /**
     * @param string $createdAt
     * @return ProductLabelInterface
     */
    public function setCreatedAt(string $createdAt): ProductLabelInterface
    {
        return $this->setData(ProductLabelInterface::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(ProductLabelInterface::UPDATED_AT) ?: '';
    }

    /**
     * @param string $updatedAt
     * @return ProductLabelInterface
     */
    public function setUpdatedAt(string $updatedAt): ProductLabelInterface
    {
        return $this->setData(ProductLabelInterface::UPDATED_AT, $updatedAt);
    }
}
