/*browser:true*/
/*global define*/
define(
    [],
    function () {
        'use strict';
        var debug = window.checkoutConfig.payment.boacompra.debug;
        var cc = window.checkoutConfig.payment.boacompra_creditcard;
        var ewallet = window.checkoutConfig.payment.boacompra_ewallet;

        return {
            debug: debug,
            cc: cc,
            ewallet: ewallet,
        };
    }
);