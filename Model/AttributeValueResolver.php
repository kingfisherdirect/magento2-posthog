<?php

namespace KingfisherDirect\Posthog\Model;

use Magento\Catalog\Model\Product;
use Psr\Log\LoggerInterface;

class AttributeValueResolver
{
    public function __construct(private LoggerInterface $logger) {}

    /**
     * Resolve a product attribute's display value for outbound tracking.
     *
     * Returns null when the attribute has no meaningful value, so callers
     * can uniformly check `!== null` regardless of the attribute's frontend
     * input type.
     */
    public function resolve(Product $product, string $attributeCode)
    {
        try {
            $value = $product->getAttributeText($attributeCode);
        } catch (\Throwable $e) {
            $this->logger->warning(
                'PostHog: getAttributeText failed',
                ['attribute' => $attributeCode, 'product_id' => $product->getId(), 'exception' => $e]
            );
            $value = null;
        }

        if ($value === false || $value === null) {
            $value = $product->getData($attributeCode);
        }

        if (is_array($value)) {
            $value = implode(', ', array_map('strval', $value));
        }

        if ($value === null || $value === false || $value === '') {
            return null;
        }

        return $value;
    }

    /**
     * Resolve a set of attributes into a 'product_' + code => value map, skipping
     * attributes with no meaningful value. Falls back to $fallback (e.g. a
     * configurable parent) when $product itself has no value for an attribute.
     */
    public function resolveProperties(Product $product, array $attributeCodes, ?Product $fallback = null): array
    {
        $properties = [];
        foreach ($attributeCodes as $attributeCode) {
            $value = $this->resolve($product, $attributeCode);
            if ($value === null && $fallback !== null) {
                $value = $this->resolve($fallback, $attributeCode);
            }
            if ($value !== null) {
                $properties['product_' . $attributeCode] = $value;
            }
        }

        return $properties;
    }
}
