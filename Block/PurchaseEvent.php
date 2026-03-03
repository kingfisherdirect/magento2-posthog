<?php
/**
 * KingfisherDirect_Posthog
 */

namespace KingfisherDirect\Posthog\Block;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class PurchaseEvent extends Template
{
    public function __construct(
        Context $context,
        private CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getOrderData(): array
    {
        $order = $this->checkoutSession->getLastRealOrder();

        if (!$order || !$order->getId()) {
            return [];
        }

        $items = [];
        $totalQuantity = 0;
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $qty = (int) $orderItem->getQtyOrdered();
            $totalQuantity += $qty;
            $items[] = [
                'sku'      => $orderItem->getSku(),
                'name'     => $orderItem->getName(),
                'price'    => round((float) $orderItem->getPriceInclTax(), 2),
                'quantity' => $qty,
            ];
        }

        return [
            'order_id'       => $order->getIncrementId(),
            'revenue'        => round((float) $order->getGrandTotal(), 2),
            'currency'       => $order->getOrderCurrencyCode(),
            'shipping'       => round((float) $order->getShippingAmount(), 2),
            'tax'            => round((float) $order->getTaxAmount(), 2),
            'delivery'       => $order->getShippingDescription(),
            'total_products' => count($items),
            'total_quantity' => $totalQuantity,
            'items'          => $items,
        ];
    }
}
