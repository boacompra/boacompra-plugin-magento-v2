/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'jquery',
        'mage/translate',
        'Magento_Checkout/js/view/payment/default',
        'Uol_BoaCompra/js/model/config',
        'Uol_BoaCompra/js/model/subtype',
    ],
    function (ko, $, $t, Component, config, subtype) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'Uol_BoaCompra/payment/ewallet',
                subtypeListTemplate: 'Uol_BoaCompra/ewallet/list',
                subtypeItemTemplate: 'Uol_BoaCompra/ewallet/item',
                subtype: subtype.name,
            },

            validate: function () {
                if (!this.subtype() || this.subtype() === '') {
                    this.messageContainer.addErrorMessage({'message': $t('Select an option')});
                    return false;
                }

                return true;
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'payment_method_type': 'e-wallet',
                        'payment_method_sub_type': subtype.name(),
                    }
                };
            },

            getSubtypes: function() {
                return config.ewallet.subtypes;
            },

            /** Redirect to BoaCompra */
            afterPlaceOrder: function () {
                $.mage.redirect(
                    config.ewallet.redirectUrl
                );
            }
        });
    }
);
