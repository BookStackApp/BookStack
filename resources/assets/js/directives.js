
var toggleSwitchTemplate = require('./components/toggle-switch.html');

module.exports = function(ngApp) {

    /**
     * Toggle Switches
     * Have basic on/off functionality.
     * Use string values of 'true' & 'false' to dictate the current state.
     */
    ngApp.directive('toggleSwitch', function() {
        return {
            restrict: 'E',
            template: toggleSwitchTemplate,
            scope: true,
            link: function(scope, element, attrs) {
                scope.name = attrs.name;
                scope.value = attrs.value;
                scope.isActive = scope.value == true && scope.value != 'false';
                scope.value = (scope.value == true && scope.value != 'false') ? 'true' : 'false';

                scope.switch = function() {
                    scope.isActive = !scope.isActive;
                    scope.value = scope.isActive ? 'true' : 'false';
                }

            }
        };
    });


};