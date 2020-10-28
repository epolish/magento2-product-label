<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model\ProductLabel;

use Exception;
use Magento\Catalog\Model\ImageUploader as CatalogImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Image\Adapter\Gd2 as ImageAdapterGd2;
use Magento\Framework\Image\Adapter\Gd2Factory as ImageAdapterGd2Factory;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Polish\ProductLabel\Model\ProductLabel\ImageUrl as ProductLabelImageUrl;
use Psr\Log\LoggerInterface;

/**
 * Class ImageUploader
 */
class ImageUploader extends CatalogImageUploader
{
    /**
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ProductLabelImageUrl
     */
    private $productLabelImageUrl;

    /**
     * @var ImageAdapterGd2Factory
     */
    private $imageAdapterGd2Factory;

    /**
     * ImageUploader constructor.
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param FileDriver $fileDriver
     * @param ImageUrl $productLabelImageUrl
     * @param ImageAdapterGd2Factory $imageAdapterGd2Factory
     * @param string $baseTmpPath
     * @param string $basePath
     * @param array $allowedExtensions
     * @param array $allowedMimeTypes
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        FileDriver $fileDriver,
        ProductLabelImageUrl $productLabelImageUrl,
        ImageAdapterGd2Factory $imageAdapterGd2Factory,
        string $baseTmpPath,
        string $basePath,
        array $allowedExtensions,
        array $allowedMimeTypes = []
    ) {
        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->productLabelImageUrl = $productLabelImageUrl;
        $this->imageAdapterGd2Factory = $imageAdapterGd2Factory;

        parent::__construct(
            $coreFileStorageDatabase,
            $filesystem,
            $uploaderFactory,
            $storeManager,
            $logger,
            $baseTmpPath,
            $basePath,
            $allowedExtensions,
            $allowedMimeTypes
        );
    }

    /**
     * @param string $fileId
     * @return string[]
     * @throws LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        return parent::saveFileToTmpDir($fileId);
    }

    /**
     * @param string $imageName
     * @param false $returnRelativePath
     * @return string
     * @throws LocalizedException
     */
    public function moveFileFromTmp($imageName, $returnRelativePath = false)
    {
        return parent::moveFileFromTmp($imageName, $returnRelativePath);
    }

    /**
     * @param string $imageName
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveProductLabelTmpImage(string $imageName): string
    {
        return sprintf(
            "/%s/%s",
            $this->storeManager->getStore()->getBaseMediaDir(),
            $this->moveFileFromTmp($imageName, true)
        );
    }

    /**
     * @param string $imageRelativePath
     * @return array|int[]
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function getProductLabelImageData(string $imageRelativePath): array
    {
        if (!$this->isImageExists($this->getImageAbsolutePath($imageRelativePath))) {
            return [];
        }

        $image = $this->getGd2Image($this->getImageAbsolutePath($imageRelativePath));

        return [
            'type' => $image->getMimeType(),
            'name' => $this->getImageNameFromRelativePath($imageRelativePath),
            'url' => $this->productLabelImageUrl->getProductLabelImageUrl($imageRelativePath),
            'size' => $this->getPubDirectoryRead()
                ->stat($this->getImageAbsolutePath($imageRelativePath))['size'],
            'width' => $image->getOriginalWidth(),
            'height' => $image->getOriginalHeight()
        ];
    }

    /**
     * @param string $imageRelativePath
     * @return string
     */
    public function getImageNameFromRelativePath(string $imageRelativePath): string
    {
        $croppedRelativePath = explode('/', $imageRelativePath);

        return end($croppedRelativePath);
    }

    /**
     * @param string $imageAbsolutePath
     * @return ImageAdapterGd2
     * @throws StateException
     */
    private function getGd2Image(string $imageAbsolutePath): ImageAdapterGd2
    {
        $image = $this->imageAdapterGd2Factory->create();

        try {
            $image->open($imageAbsolutePath);
        } catch (Exception $ex) {
            throw new StateException(__('Cannot load the image.'));
        }

        return $image;
    }

    /**
     * @param string $imageAbsolutePath
     * @return bool
     * @throws FileSystemException
     */
    private function isImageExists(string $imageAbsolutePath): bool
    {
        return $this->fileDriver->isExists($imageAbsolutePath);
    }

    /**
     * @param string $imageRelativePath
     * @return string
     */
    private function getImageAbsolutePath(string $imageRelativePath): string
    {
        return $this->getPubDirectoryRead()->getAbsolutePath($imageRelativePath);
    }

    /**
     * @return DirectoryReadInterface
     */
    private function getPubDirectoryRead(): DirectoryReadInterface
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::PUB);
    }
}
