/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'mage/translate',
        'Uol_BoaCompra/js/model/message',
        'Uol_BoaCompra/js/model/config',
        'Uol_BoaCompra/js/model/cc-saved',
        'Uol_BoaCompra/js/model/cc-form'
    ],
    function (ko, Component, $t, message, config, CcSaved, CcForm) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Uol_BoaCompra/payment/creditcard',
                containerFormCc: 'Uol_BoaCompra/creditcard/form',
                containerSavedCc: 'Uol_BoaCompra/creditcard/saved',
                CcSaved: CcSaved,
                CcForm: CcForm,
                useSavedCc: true,
                creditCardType: '',
                creditCardBrand: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardVerificationNumber: '',
                creditCardInstallments: '',
            },

            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe([
                        'useSavedCc',
                        'creditCardType',
                        'creditCardBrand',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardInstallments',
                    ]);

                return this;
            },

            initialize: function () {
                this._super();

                var self = this;

                this.loadSavedCreditCard();

                this.useSavedCc.subscribe(function (data) {
                   if (data) self.updateDataWithSavedCc();
                });

                CcForm.number.subscribe(function (data) {
                    self.creditCardNumber(data.substr(-4));
                    if (data.length > 5) {
                        CcForm.loadBrandAndInstallments();
                    }
                    CcForm.generateToken();
                });

                CcForm.brand.subscribe(function (data) {
                    self.creditCardBrand(data);
                });

                CcForm.exp_year.subscribe(function (data) {
                    self.creditCardExpYear(data);
                    CcForm.generateToken();
                });

                CcForm.exp_month.subscribe(function (data) {
                    self.creditCardExpMonth(data);
                    CcForm.generateToken();
                });

                CcForm.cvv.subscribe(function (data) {
                    self.creditCardVerificationNumber(data);
                    CcForm.generateToken();
                });

                CcForm.type.subscribe(function (data) {
                    self.creditCardType(data);
                });

                message.info.subscribe(function (data) {
                    if (data) {
                        self.messageContainer.addErrorMessage({'message': data});
                        message.info('');
                    }
                })
            },

            loadSavedCreditCard: function () {
                if (config.cc.savedCreditCard) {
                    CcSaved.load(config.cc.savedCreditCard);
                    CcSaved.loadInstallments();
                    this.updateDataWithSavedCc();
                } else {
                    this.useSavedCc(false);
                }
            },

            updateDataWithSavedCc: function () {
                this.creditCardNumber(CcSaved.number());
                this.creditCardExpYear(CcSaved.exp_year());
                this.creditCardExpMonth(CcSaved.exp_month());
                this.creditCardType(CcSaved.type());
                this.creditCardBrand(CcSaved.brand());
            },

            validate: function() {
                if (this.useSavedCc()) {
                    if (!CcSaved.validate()) return false;
                } else {
                    if (!CcForm.validate()) return false;
                }

                if (this.creditCardInstallments() === '') {
                    message.info($t('Please enter the Installments.'));
                    return false;
                }

                return true;
            },

            /**
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'payment_method_type': 'credit-card',
                        'payment_method_sub_type': this.creditCardBrand(),
                        'use_saved_cc': this.useSavedCc(),
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_installments': this.creditCardInstallments(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_last_4': this.creditCardNumber(),
                        'cc_code': CcSaved.code(),
                        'cc_token': CcForm.token(),
                        'cc_save': CcForm.to_save()
                    }
                };
            },

            getIcon: function (type) {
                return window.checkoutConfig.payment.ccform.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.ccform.icons[type]
                    : false;
            },

            getSaveCreditCardIsAvailable: function () {
                return config.cc.tokenAvailable;
            }
        });
    }
);
