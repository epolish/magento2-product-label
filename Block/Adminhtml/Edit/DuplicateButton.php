<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Block\Adminhtml\Edit;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DuplicateButton
 */
class DuplicateButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * DuplicateButton constructor.
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return ((int)$this->request->getParam('id')) ? [
            'label' => __('Duplicate'),
            'class' => 'duplicate',
            'id' => 'product-label-edit-duplicate-button',
            'on_click' => sprintf(
                "deleteConfirm('%s', '%s')",
                __('Are you sure you want to duplicate this product label?'),
                $this->urlBuilder->getUrl('*/*/duplicate', ['id' => (int)$this->request->getParam('id')])
            ),
            'sort_order' => 20,
            'aclResource' => 'Polish_ProductLabel::edit',
        ] : [];
    }
}
