/*browser:true*/
/*global define*/
define([
    'ko'
], function (ko) {
    'use strict';

    var name = ko.observable('');

    return {
        name: name
    };
});
