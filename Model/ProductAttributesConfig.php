<?php

namespace KingfisherDirect\Posthog\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class ProductAttributesConfig
{
    private const CONFIG_PATH = 'posthog/product_attributes/attributes';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private SerializerInterface $serializer,
        private LoggerInterface $logger
    ) {}

    public function getAttributesForProductPage(): array
    {
        return $this->getAttributeCodesFor('register_on_product_page');
    }

    public function getAttributesForPurchase(): array
    {
        return $this->getAttributeCodesFor('include_in_purchase');
    }

    private function getAttributeCodesFor(string $flagKey): array
    {
        $codes = [];
        foreach ($this->getAttributes() as $row) {
            if (empty($row[$flagKey])) {
                continue;
            }
            $code = trim((string) ($row['attribute_code'] ?? ''));
            if ($code !== '') {
                $codes[] = $code;
            }
        }

        return $codes;
    }

    private function getAttributes(): array
    {
        $value = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE);

        if (empty($value)) {
            return [];
        }

        try {
            $data = $this->serializer->unserialize($value);
        } catch (\Throwable $e) {
            $this->logger->error(
                'PostHog: failed to unserialize posthog/product_attributes/attributes config',
                ['exception' => $e]
            );
            return [];
        }

        if (!is_array($data)) {
            $this->logger->error(
                'PostHog: posthog/product_attributes/attributes config did not unserialize to an array'
            );
            return [];
        }

        unset($data['__empty']);

        return array_values(array_filter($data, 'is_array'));
    }
}
