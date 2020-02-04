/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Uol_BoaCompra/payment/postpay'
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'payment_method_type': 'postpay',
                        'payment_method_sub_type': 'boleto',
                    }
                };
            },

        });
    }
);
