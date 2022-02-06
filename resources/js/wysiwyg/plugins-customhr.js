/**
 * @param {Editor} editor
 * @param {String} url
 */
function register(editor, url) {
    editor.addCommand('InsertHorizontalRule', function () {
        let hrElem = document.createElement('hr');
        let cNode = editor.selection.getNode();
        let parentNode = cNode.parentNode;
        parentNode.insertBefore(hrElem, cNode);
    });

    editor.ui.registry.addButton('hr', {
        icon: 'horizontal-rule',
        tooltip: 'Insert horizontal line',
        onAction() {
            editor.execCommand('InsertHorizontalRule');
        }
    });
}


/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}