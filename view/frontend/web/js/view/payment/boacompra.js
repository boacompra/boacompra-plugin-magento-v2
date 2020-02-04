/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'boacompra_hosted',
                component: 'Uol_BoaCompra/js/view/payment/method-renderer/hosted'
            },
            {
                type: 'boacompra_creditcard',
                component: 'Uol_BoaCompra/js/view/payment/method-renderer/creditcard'
            },
            {
                type: 'boacompra_postpay',
                component: 'Uol_BoaCompra/js/view/payment/method-renderer/postpay'
            },
            {
                type: 'boacompra_ewallet',
                component: 'Uol_BoaCompra/js/view/payment/method-renderer/ewallet'
            }
        );

        return Component.extend({});
    }
);