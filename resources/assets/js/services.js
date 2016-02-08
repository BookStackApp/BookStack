"use strict";

module.exports = function(ngApp, events) {

    ngApp.factory('imageManagerService', function() {
        return {
            show: false,
            showExternal: false
        };
    });

};