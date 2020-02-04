/*browser:true*/
/*global define*/
define([
    'ko',
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Uol_BoaCompra/js/model/logger',
    'Uol_BoaCompra/js/model/message',
    'Uol_BoaCompra/js/model/config',
    'Uol_BoaCompra/js/model/boacompra-sdk',
], function (ko, $, $t, fullScreenLoader, logger, message, config, Boacompra) {
    'use strict';

    var token = ko.observable('');
    var number = ko.observable('');
    var brand = ko.observable('');
    var type = ko.observable('');
    var exp_month = ko.observable('');
    var exp_year = ko.observable('');
    var cvv = ko.observable('');
    var to_save = ko.observable('');
    var installments = ko.observableArray([]);

    return {
        token: token,
        brand: brand,
        type: type,
        number: number,
        exp_month: exp_month,
        exp_year: exp_year,
        cvv: cvv,
        to_save: to_save,
        installments: installments,

        validate: function () {
            if (!this.number() || this.number() === '') {
                message.info($t('Please enter the Credit Card Number.'));
                return false;
            }

            if (!this.exp_month() || this.exp_month() === '') {
                message.info($t('Please enter the Expiration Date Month.'));
                return false;
            }

            if (!this.exp_year() || this.exp_year() === '') {
                message.info($t('Please enter the Expiration Date Year.'));
                return false;
            }

            if (!this.cvv() || this.cvv() === '') {
                message.info($t('Please enter the Card Verification Number.'));
                return false;
            }

            if (this.token() === '') {
                message.info($t('Please enter valid Credit Card data.'));
                return false;
            }

            if (!this.type() || this.type() === '') {
                message.info($t('Please enter valid Credit Card.'));
                return false;
            }

            return true;
        },

        generateToken: function () {
            var self = this;

            this.token('');

            if (this.number() === ''
                || !this.exp_month()
                || this.exp_month() === ''
                || !this.exp_year()
                || this.exp_year() === ''
                || this.cvv() === ''
            ) {
                return false;
            }

            fullScreenLoader.startLoader();

            var data = {
                creditCard: this.number(),
                cvv: this.cvv(),
                expiration: {
                    month: this.exp_month(),
                    year: this.exp_year()
                }
            };

            Boacompra.paymentMethod.getDirectToken(data, function (error, directToken) {
                logger.log(error);
                logger.log(directToken);

                fullScreenLoader.stopLoader();

                if (error) return false;

                self.token(directToken);
            });
        },

        loadBrandAndInstallments: function () {
            var self = this;

            fullScreenLoader.startLoader();

            $.post({
                url: config.cc.brandAndInstallmentsUrl,
                data: {
                    bin: this.number().substring(0,6)
                },
                dataType: 'json'
            }).done(function (data) {
                logger.log(data);

                if (data.error) {
                    message.info(data.message);
                    fullScreenLoader.stopLoader();
                    return;
                }

                self.handleBrand(data.bin);
                self.handleInstallments(data.installments);

                fullScreenLoader.stopLoader();
            }).error(function() {
                fullScreenLoader.stopLoader();
                message.info($t('Unable to recover the installments'));
            });
        },

        handleInstallments: function (installments) {
            this.installments(installments);
        },

        handleBrand: function (bin) {
            this.type(bin.cc_type);
            this.brand(bin.brand);
        },

        /**
         * @returns {Object}
         */
        getMonthsValues: function () {
            return _.map(config.cc.months, function (value, key) {
                return {
                    'value': key,
                    'month': value
                };
            });
        },

        /**
         * @returns {Object}
         */
        getYearsValues: function () {
            return _.map(config.cc.years, function (value, key) {
                return {
                    'value': key,
                    'year': value
                };
            });
        },

    };
});
