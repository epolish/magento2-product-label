<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Block\Adminhtml\Promo\Catalog\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\CatalogRule\Api\Data\RuleInterface as CatalogRuleInterface;
use Magento\CatalogRule\Api\Data\RuleInterfaceFactory as CatalogRuleInterfaceFactory;
use Magento\CatalogRule\Block\Adminhtml\Promo\Catalog\Edit\Tab\Conditions as BaseClass;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as ConditionsBlock;
use Polish\ProductLabel\Api\ProductLabelRepositoryInterface;

/**
 * Class Conditions
 */
class Conditions extends BaseClass
{
    /**
     * @var CatalogRuleInterfaceFactory
     */
    private $catalogRuleFactory;

    /**
     * @var ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * Conditions constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param ConditionsBlock $conditions
     * @param Fieldset $rendererFieldset
     * @param CatalogRuleInterfaceFactory $catalogRuleFactory
     * @param ProductLabelRepositoryInterface $productLabelRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ConditionsBlock $conditions,
        Fieldset $rendererFieldset,
        CatalogRuleInterfaceFactory $catalogRuleFactory,
        ProductLabelRepositoryInterface $productLabelRepository,
        array $data = []
    ) {
        $this->catalogRuleFactory = $catalogRuleFactory;
        $this->productLabelRepository = $productLabelRepository;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $conditions,
            $rendererFieldset,
            $data
        );
    }

    /**
     * @return Form
     */
    protected function _prepareForm()
    {
        try {
            $this->addToRegistryCurrentPromoCatalogRule(
                $this->productLabelRepository->getById(
                    (int)$this->getRequest()->getParam('id')
                )->getCatalogRule()
            );
        } catch (NotFoundException $ex) {
            $this->addToRegistryCurrentPromoCatalogRule($this->catalogRuleFactory->create());
        }

        return parent::_prepareForm();
    }

    /**
     * @param CatalogRuleInterface $model
     * @param string $fieldsetId
     * @param string $formName
     * @return DataForm
     * @throws LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'product_label_form')
    {
        $result = parent::addTabToForm($model, $fieldsetId, $formName);

        $result->getElement($fieldsetId)->getRenderer()->setTemplate('Polish_ProductLabel::promo/fieldset.phtml');

        return $result;
    }

    /**
     * @param CatalogRuleInterface $catalogRule
     */
    private function addToRegistryCurrentPromoCatalogRule(CatalogRuleInterface $catalogRule)
    {
        $this->_coreRegistry->register('current_promo_catalog_rule', $catalogRule);
    }
}
