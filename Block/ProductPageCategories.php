<?php

namespace KingfisherDirect\Posthog\Block;

use KingfisherDirect\Posthog\Helper\Data as PosthogHelper;
use KingfisherDirect\Posthog\Model\AttributeValueResolver;
use KingfisherDirect\Posthog\Model\ProductAttributesConfig;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class ProductPageCategories extends AbstractPosthogBlock
{
    public function __construct(
        Context $context,
        private Registry $registry,
        private ProductAttributesConfig $attributesConfig,
        PosthogHelper $posthogHelper,
        private AttributeValueResolver $attributeValueResolver,
        array $data = []
    ) {
        parent::__construct($context, $posthogHelper, $data);
    }

    public function getProperties(): array
    {
        $product = $this->registry->registry('product');
        if (!$product) {
            return [];
        }

        $properties = [];
        foreach ($this->attributesConfig->getAttributesForProductPage() as $attributeCode) {
            $value = $this->attributeValueResolver->resolve($product, $attributeCode);
            if ($value !== null) {
                $properties['product_' . $attributeCode] = $value;
            }
        }

        return $properties;
    }
}
