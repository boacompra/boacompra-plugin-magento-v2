/*browser:true*/
/*global define*/
define([
    'boacompraSdk'
], function () {
    'use strict';

    var paymentMethod = new Boacompra.PaymentMethod();

    return {
        paymentMethod: paymentMethod
    };
});
