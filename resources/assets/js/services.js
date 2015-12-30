"use strict";

module.exports = function(ngApp) {

    ngApp.factory('imageManagerService', function() {
        return {
            show: false,
            showExternal: false
        };
    });

};