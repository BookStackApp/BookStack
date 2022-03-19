/**
 * @param {Editor} editor
 * @param {String} url
 */

function register(editor, url) {

    editor.on('PreInit', () => {

        editor.parser.addNodeFilter('li', function(nodes) {
            for (const node of nodes) {
                if (node.attributes.map.class === 'task-list-item') {
                    parseTaskListNode(node);
                }
            }
        });

        editor.serializer.addNodeFilter('li', function(nodes) {
            for (const node of nodes) {
                if (node.attributes.map.class === 'task-list-item') {
                    serializeTaskListNode(node);
                }
            }
        });

    });

}

/**
 * @param {AstNode} node
 */
function parseTaskListNode(node) {
    // Force task list item class
    node.attr('class', 'task-list-item');

    // Copy checkbox status and remove checkbox within editor
    for (const child of node.children()) {
        if (child.name === 'input') {
            if (child.attr('checked') === 'checked') {
                node.attr('checked', 'checked');
            }
            child.remove();
        }
    }
}

/**
 * @param {AstNode} node
 */
function serializeTaskListNode(node) {
    const isChecked = node.attr('checked') === 'checked';
    node.attr('checked', null);

    const inputAttrs = {type: 'checkbox', disabled: 'disabled'};
    if (isChecked) {
        inputAttrs.checked = 'checked';
    }

    const checkbox = new tinymce.html.Node.create('input', inputAttrs);
    checkbox.shortEnded = true;
    node.firstChild ? node.insert(checkbox, node.firstChild, true) : node.append(checkbox);
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}