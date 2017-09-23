

module.exports = function (ngApp, events) {


    /**
     * Page Editor Toolbox
     * Controls all functionality for the sliding toolbox
     * on the page edit view.
     */
    ngApp.directive('toolbox', [function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {

                // Get common elements
                const $buttons = elem.find('[toolbox-tab-button]');
                const $content = elem.find('[toolbox-tab-content]');
                const $toggle = elem.find('[toolbox-toggle]');

                // Handle toolbox toggle click
                $toggle.click((e) => {
                    elem.toggleClass('open');
                });

                // Set an active tab/content by name
                function setActive(tabName, openToolbox) {
                    $buttons.removeClass('active');
                    $content.hide();
                    $buttons.filter(`[toolbox-tab-button="${tabName}"]`).addClass('active');
                    $content.filter(`[toolbox-tab-content="${tabName}"]`).show();
                    if (openToolbox) elem.addClass('open');
                }

                // Set the first tab content active on load
                setActive($content.first().attr('toolbox-tab-content'), false);

                // Handle tab button click
                $buttons.click(function (e) {
                    let name = $(this).attr('toolbox-tab-button');
                    setActive(name, true);
                });
            }
        }
    }]);
};
