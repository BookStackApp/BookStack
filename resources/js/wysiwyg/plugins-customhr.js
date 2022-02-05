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
        tooltip: 'Horizontal line',
        onAction() {
            editor.execCommand('InsertHorizontalRule');
        }
    });

    editor.ui.registry.addMenuItem('hr', {
        icon: 'horizontal-rule',
        text: 'Horizontal line',
        context: 'insert',
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