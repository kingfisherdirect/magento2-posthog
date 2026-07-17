<?php

namespace KingfisherDirect\Posthog\Block;

use KingfisherDirect\Posthog\Helper\Data as PosthogHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

abstract class AbstractPosthogBlock extends Template
{
    public function __construct(
        Context $context,
        protected PosthogHelper $posthogHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _toHtml(): string
    {
        if (!$this->posthogHelper->isConfigured()) {
            return '';
        }
        return parent::_toHtml();
    }
}
