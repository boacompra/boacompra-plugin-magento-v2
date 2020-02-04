/*browser:true*/
/*global define*/
define([
    'ko'
], function (ko) {
    'use strict';

    var info = ko.observable('');

    return {
        info: info
    };
});
