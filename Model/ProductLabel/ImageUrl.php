<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Model\ProductLabel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ImageUrl
 */
class ImageUrl
{
    const PLACEHOLDER_URL_PATH_PATTERN = 'catalog/product_label/placeholder/';

    const PLACEHOLDER_SCOPE_CONFIG_PATH = 'product_label/placeholder/%s_placeholder';

    /**
     * @var array
     */
    private $placeholders;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ImageUrl constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;

        $this->initialize();
    }

    /**
     *
     */
    private function initialize()
    {
        $this->placeholders = [];
    }

    /**
     * @param string $imageRelativePath
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductLabelImageUrl(string $imageRelativePath): string
    {
        $urlType = UrlInterface::URL_TYPE_WEB;

        if (!$imageRelativePath) {
            $imageRelativePath = self::PLACEHOLDER_URL_PATH_PATTERN . $this->getPlaceholderUrl();
            $urlType = UrlInterface::URL_TYPE_MEDIA;
        }

        return $this->storeManager->getStore()->getBaseUrl($urlType)
            . trim($imageRelativePath, '/');
    }

    /**
     * @param string $imageType
     * @return string
     */
    public function getPlaceholderUrl(string $imageType = 'thumbnail'): string
    {
        if (!array_key_exists($imageType, $this->placeholders)) {
            $this->placeholders[$imageType] = (string)$this->scopeConfig->getValue(
                sprintf(self::PLACEHOLDER_SCOPE_CONFIG_PATH, $imageType),
                ScopeInterface::SCOPE_STORE
            );
        }

        return $this->placeholders[$imageType];
    }
}
