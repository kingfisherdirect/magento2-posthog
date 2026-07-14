define(['jquery', 'Magento_Customer/js/customer-data'], function ($, customerData) {
    'use strict';

    $(document).on('ajax:addToCart', function (event, data) {
        if (!window.posthog || !data.form || !data.form[0]) {
            return;
        }

        var qty = Number(new FormData(data.form[0]).get('qty')) || undefined;
        var productId = data.productIds && data.productIds[0];

        var subscription = customerData.get('cart').subscribe(function (cart) {
            subscription.dispose();

            var item = (cart.items || []).find(function (i) {
                return String(i.product_id) === String(productId);
            });

            if (!item) {
                return;
            }

            window.posthog.capture('add_to_basket', {
                product_id: productId,
                product_name: item.product_name,
                sku: item.product_sku,
                quantity: qty,
                price: typeof item.product_price_value === 'object' ? item.product_price_value.incl_tax : item.product_price_value,
                currency: window.posthogConfig && window.posthogConfig.currency || undefined,
                $current_url: window.location.href
            });
        });
    });
});
