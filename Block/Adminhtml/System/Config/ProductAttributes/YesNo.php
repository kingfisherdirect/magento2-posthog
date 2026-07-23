<?php

namespace KingfisherDirect\Posthog\Block\Adminhtml\System\Config\ProductAttributes;

use Magento\Framework\View\Element\Html\Select;

class YesNo extends Select
{
    public function setInputName(string $value): static
    {
        return $this->setName($value);
    }

    public function setInputId(string $value): static
    {
        return $this->setId($value);
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions([
                ['value' => '0', 'label' => __('No')],
                ['value' => '1', 'label' => __('Yes')],
            ]);
        }
        return parent::_toHtml();
    }
}
