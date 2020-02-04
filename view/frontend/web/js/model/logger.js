/*browser:true*/
/*global define*/
define(
    [
        'Uol_BoaCompra/js/model/config'
    ],
    function (config) {
        'use strict';

        return {
            log: function (message) {
                if (config.debug) {
                    console.log(message);
                }
            }
        };
    }
);