/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'Uol_BoaCompra/payment/hosted'
            },

            /** Redirect to BoaCompra */
            afterPlaceOrder: function () {
                $.mage.redirect(
                    window.checkoutConfig.payment['boacompra_hosted'].redirectUrl
                );
            }
        });
    }
);
