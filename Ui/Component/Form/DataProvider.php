<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Ui\Component\Form;

use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Polish\ProductLabel\Api\Data\ProductLabelInterface;
use Polish\ProductLabel\Model\ProductLabel\ImageUploader;
use Polish\ProductLabel\Model\ResourceModel\ProductLabel\CollectionFactory as ProductLabelCollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * DataProvider constructor.
     * @param LoggerInterface $logger
     * @param ImageUploader $imageUploader
     * @param ProductLabelCollectionFactory $productLabelCollectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        LoggerInterface $logger,
        ImageUploader $imageUploader,
        ProductLabelCollectionFactory $productLabelCollectionFactory,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->logger = $logger;
        $this->imageUploader = $imageUploader;
        $this->collection = $productLabelCollectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var ProductLabelInterface $item */
        foreach ($this->collection->getItems() as $item) {
            $this->loadedData[$item->getEntityId()] = $item->getData();

            $imageData = $this->getImageData((string)$item->getImage());

            if (count($imageData)) {
                $this->loadedData[$item->getEntityId()][ProductLabelInterface::IMAGE] = [0 => $imageData];
            }
        }

        return $this->loadedData;
    }

    /**
     * @param string $imageRelativePath
     * @return array|int[]
     * @throws NoSuchEntityException
     * @throws FileSystemException
     * @throws StateException
     */
    private function getImageData(string $imageRelativePath)
    {
        $imageData = [];

        try {
            if ($imageRelativePath) {
                $imageData = $this->imageUploader->getProductLabelImageData($imageRelativePath);
            }
        } catch (Exception $ex) {
            $this->logger->error(sprintf(
                "Message: %s; File: %s; Line: %s; Param: %s;",
                $ex->getMessage(),
                $ex->getFile(),
                $ex->getLine(),
                $imageRelativePath
            ));

            throw $ex;
        }

        return count($imageData) ? [
            'type' => $imageData['type'],
            'name' => $imageData['name'],
            'url' => $imageData['url'],
            'size' => $imageData['size'],
            'previewType' => 'image',
            'previewWidth' => $imageData['width'],
            'previewHeight' => $imageData['height']
        ] : [];
    }
}
