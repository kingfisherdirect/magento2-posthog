define(['jquery', 'Magento_Customer/js/customer-data'], function ($, customerData) {
    'use strict';

    $(document).on('ajax:addToCart', function (event, data) {
        if (!window.posthog || !data.form || !data.form[0]) {
            return;
        }

        var formData = new FormData(data.form[0]);
        var qty = Number(formData.get('qty')) || undefined;
        // on product pages productIds starts with the viewed product, also for other add to basket forms there
        var productId = formData.get('product') || data.productIds && data.productIds[0];
        var relatedIds = String(formData.get('related_product') || '').split(',').filter(Boolean);

        function capture(cart, id, properties) {
            var item = (cart.items || []).find(function (i) {
                return String(i.product_id) === String(id);
            });

            if (!item) {
                return;
            }

            window.posthog.capture('add_to_basket', $.extend({
                product_id: id,
                product_name: item.product_name,
                sku: item.product_sku,
                price: typeof item.product_price_value === 'object' ? item.product_price_value.incl_tax : item.product_price_value,
                currency: window.posthogConfig && window.posthogConfig.currency || undefined,
                $current_url: window.location.href
            }, properties));
        }

        var subscription = customerData.get('cart').subscribe(function (cart) {
            subscription.dispose();

            capture(cart, productId, {quantity: qty});

            // ticked related products are added by the same request, one of each
            relatedIds.forEach(function (relatedId) {
                capture(cart, relatedId, {quantity: 1, related_to_product_id: productId});
            });
        });
    });
});
