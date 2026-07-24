<?php

namespace KingfisherDirect\Posthog\Plugin;

use KingfisherDirect\Posthog\Block\PurchaseEvent;
use KingfisherDirect\Posthog\Helper\Data as PosthogHelper;
use KingfisherDirect\Posthog\Model\AttributeValueResolver;
use KingfisherDirect\Posthog\Model\ProductAttributesConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableType;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionProvider;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class PurchaseEventAttributesPlugin
{
    public function __construct(
        private CollectionFactory $productCollectionFactory,
        private ProductAttributesConfig $attributesConfig,
        private AttributeValueResolver $attributeValueResolver,
        private ConfigurableType $configurableType,
        private OptionProvider $optionProvider,
        private PosthogHelper $posthogHelper,
        private LoggerInterface $logger,
        private StoreManagerInterface $storeManager
    ) {}

    public function afterGetOrderData(PurchaseEvent $subject, array $result): array
    {
        if (empty($result['items'])) {
            return $result;
        }

        if (!$this->posthogHelper->isConfigured()) {
            return $result;
        }

        $attributeCodes = $this->attributesConfig->getAttributesForPurchase();
        if (empty($attributeCodes)) {
            return $result;
        }

        try {
            return $this->enrichItems($result, $attributeCodes);
        } catch (\Throwable $e) {
            $this->logger->error(
                'PostHog: failed to enrich purchase event with product attributes',
                ['exception' => $e]
            );
            return $result;
        }
    }

    private function enrichItems(array $result, array $attributeCodes): array
    {
        $skus = array_column($result['items'], 'sku');
        $storeId = (int) $this->storeManager->getStore()->getId();

        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect($attributeCodes);
        $collection->addFieldToFilter('sku', ['in' => $skus]);

        $productsBySku = [];
        foreach ($collection as $product) {
            $productsBySku[$product->getSku()] = $product;
        }

        $parentsBySku = $this->loadConfigurableParents($productsBySku, $attributeCodes, $storeId);

        foreach ($result['items'] as &$item) {
            $product = $productsBySku[$item['sku']] ?? null;
            if (!$product) {
                continue;
            }
            $parent = $parentsBySku[$item['sku']] ?? null;
            foreach ($attributeCodes as $attributeCode) {
                $value = $this->attributeValueResolver->resolve($product, $attributeCode);
                if ($value === null && $parent !== null) {
                    $value = $this->attributeValueResolver->resolve($parent, $attributeCode);
                }
                if ($value !== null) {
                    $item['product_' . $attributeCode] = $value;
                }
            }
        }
        unset($item);

        return $result;
    }

    /**
     * Configurable parent products, keyed by order item sku.
     *
     * Resolved via the real catalog_product_super_link relationship so it stays correct
     * regardless of purchase path (configurable option selector, direct simple-product
     * purchase, admin order create, API) — the order item's own product_id is not a
     * reliable indicator of parent-vs-own id across all of those paths.
     *
     * @return Product[]
     */
    private function loadConfigurableParents(array $productsBySku, array $attributeCodes, int $storeId): array
    {
        $idToSku = [];
        foreach ($productsBySku as $sku => $product) {
            if ($product->getTypeId() === 'simple') {
                $idToSku[(int) $product->getId()] = $sku;
            }
        }

        if (empty($idToSku)) {
            return [];
        }

        $connection = $this->configurableType->getConnection();
        $select = $connection->select()
            ->from(['l' => $this->configurableType->getMainTable()], ['child_id' => 'product_id'])
            ->join(
                ['e' => $this->configurableType->getTable('catalog_product_entity')],
                'e.' . $this->optionProvider->getProductEntityLinkField() . ' = l.parent_id',
                ['parent_entity_id' => 'entity_id']
            )
            ->where('l.product_id IN(?)', array_keys($idToSku));

        $skuToParentId = [];
        foreach ($connection->fetchAll($select) as $row) {
            $sku = $idToSku[(int) $row['child_id']] ?? null;
            if ($sku !== null && !isset($skuToParentId[$sku])) {
                $skuToParentId[$sku] = (int) $row['parent_entity_id'];
            }
        }

        if (empty($skuToParentId)) {
            return [];
        }

        $parentCollection = $this->productCollectionFactory->create();
        $parentCollection->setStoreId($storeId);
        $parentCollection->addAttributeToSelect($attributeCodes);
        $parentCollection->addFieldToFilter('entity_id', ['in' => array_unique(array_values($skuToParentId))]);

        $parentById = [];
        foreach ($parentCollection as $parent) {
            $parentById[(int) $parent->getId()] = $parent;
        }

        $parentsBySku = [];
        foreach ($skuToParentId as $sku => $parentId) {
            if (isset($parentById[$parentId])) {
                $parentsBySku[$sku] = $parentById[$parentId];
            }
        }

        return $parentsBySku;
    }
}
