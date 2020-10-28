<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Ui\Component\Listing\Columns;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Polish\ProductLabel\Model\ProductLabel\ImageUrl as ProductLabelImageUrl;

/**
 * Class ImageColumn
 */
class ImageColumn extends Column
{
    const ALT_FIELD = 'title';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ProductLabelImageUrl
     */
    private $productLabelImageUrl;

    /**
     * ImageColumn constructor.
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductLabelImageUrl $productLabelImageUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductLabelImageUrl $productLabelImageUrl,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->productLabelImageUrl = $productLabelImageUrl;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as & $item) {
                $url = $this->productLabelImageUrl->getProductLabelImageUrl($item[$fieldName]);

                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $this->getAlt($item);
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'product_label/index/edit',
                    ['id' => $item['entity_id']]
                );
                $item[$fieldName . '_orig_src'] = $url;
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     * @return string
     */
    private function getAlt(array $row): string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;

        return isset($row[$altField]) ? $row[$altField] : '';
    }
}
