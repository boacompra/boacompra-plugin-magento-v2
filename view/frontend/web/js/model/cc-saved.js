/*browser:true*/
/*global define*/
define([
    'ko',
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Uol_BoaCompra/js/model/logger',
    'Uol_BoaCompra/js/model/config',
    'Uol_BoaCompra/js/model/message',
], function (ko, $, $t, fullScreenLoader, logger, config, message) {
    'use strict';

    var code = ko.observable('');
    var brand = ko.observable('');
    var type = ko.observable('');
    var number = ko.observable('');
    var exp_month = ko.observable('');
    var exp_year = ko.observable('');
    var installments = ko.observableArray([]);

    return {
        code: code,
        brand: brand,
        type: type,
        number: number,
        exp_month: exp_month,
        exp_year: exp_year,
        installments: installments,

        load: function (data) {
            if (data === false || data == null) {
                return;
            }

            this.code(data['code']);
            this.brand(data['brand']);
            this.type(data['cc-type']);
            this.number(data['last-number']);
            this.exp_month(data['exp-month']);
            this.exp_year(data['exp-year']);
        },

        validate: function () {
            if (!this.code()
                || !this.brand()
                || !this.type()
                || !this.number()
                || !this.exp_month()
                || !this.exp_year()
                || this.code() === ''
                || this.brand() === ''
                || this.type() === ''
                || this.number() === ''
                || this.exp_month() === ''
                || this.exp_year() === ''
            ) {
                message.info($t('Credit Card not available.'));
                return false;
            }

            return true;
        },

        loadInstallments: function () {
            var self = this;

            fullScreenLoader.startLoader();

            $.post({
                url: config.cc.installmentsUrl,
                data: {
                    brand: this.brand()
                },
                dataType: 'json'
            }).done(function (data) {
                logger.log(data);
                if (data.error) {
                    message.info(data.message);
                    fullScreenLoader.stopLoader();
                    return;
                }
                self.installments(data.installments);
                fullScreenLoader.stopLoader();
            }).error(function() {
                fullScreenLoader.stopLoader();
                message.info($t('Unable to recover the installments'));
            });
        },
    };
});
