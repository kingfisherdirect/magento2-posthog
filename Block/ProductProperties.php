<?php

namespace KingfisherDirect\Posthog\Block;

use KingfisherDirect\Posthog\Helper\Data as PosthogHelper;
use KingfisherDirect\Posthog\Model\AttributeValueResolver;
use KingfisherDirect\Posthog\Model\ProductAttributesConfig;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class ProductProperties extends AbstractPosthogBlock
{
    private ?array $attributeCodes = null;

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

    public function getPropertyKeys(): array
    {
        return array_map(
            static fn (string $attributeCode): string => 'product_' . $attributeCode,
            $this->getAttributeCodes()
        );
    }

    public function getPropertiesToRegister(): array
    {
        $product = $this->registry->registry('product');
        if (!$product) {
            return [];
        }

        return $this->attributeValueResolver->resolveProperties($product, $this->getAttributeCodes());
    }

    private function getAttributeCodes(): array
    {
        return $this->attributeCodes ??= $this->attributesConfig->getAttributesForProductPage();
    }
}
