<?php

namespace KingfisherDirect\Posthog\Block\Adminhtml\System\Config;

use KingfisherDirect\Posthog\Block\Adminhtml\System\Config\ProductAttributes\YesNo;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class ProductAttributes extends AbstractFieldArray
{
    private ?YesNo $yesNoRenderer = null;

    protected function _prepareToRender(): void
    {
        $this->addColumn('attribute_code', [
            'label' => __('Attribute Code'),
            'class' => 'required-entry',
        ]);
        $this->addColumn('register_on_product_page', [
            'label'    => __('Register on Product Page'),
            'renderer' => $this->getYesNoRenderer(),
        ]);
        $this->addColumn('include_in_purchase', [
            'label'    => __('Include in Purchase Event'),
            'renderer' => $this->getYesNoRenderer(),
        ]);

        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add Attribute');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $renderer = $this->getYesNoRenderer();

        foreach (['register_on_product_page', 'include_in_purchase'] as $column) {
            $value = $row->getData($column);
            if ($value !== null) {
                $options['option_' . $renderer->calcOptionHash($value)] = 'selected="selected"';
            }
        }

        $row->setData('option_extra_attrs', $options);
    }

    private function getYesNoRenderer(): YesNo
    {
        if ($this->yesNoRenderer === null) {
            $this->yesNoRenderer = $this->getLayout()->createBlock(
                YesNo::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->yesNoRenderer;
    }
}
