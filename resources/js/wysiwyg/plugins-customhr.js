/**
 * @param {Editor} editor
 */
function register(editor) {
    editor.addCommand('InsertHorizontalRule', () => {
        const hrElem = document.createElement('hr');
        const cNode = editor.selection.getNode();
        const {parentNode} = cNode;
        parentNode.insertBefore(hrElem, cNode);
    });

    editor.ui.registry.addButton('customhr', {
        icon: 'horizontal-rule',
        tooltip: 'Insert horizontal line',
        onAction() {
            editor.execCommand('InsertHorizontalRule');
        },
    });
}

/**
 * @return {register}
 */
export function getPlugin() {
    return register;
}
