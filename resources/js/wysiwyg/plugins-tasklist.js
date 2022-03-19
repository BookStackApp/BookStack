/**
 * @param {Editor} editor
 */
function defineTaskListCustomElement(editor) {
    const doc = editor.getDoc();
    const win = doc.defaultView;

    class TaskListElement extends win.HTMLElement {
        constructor() {
            super();
            // this.attachShadow({mode: 'open'});
            //
            // const input = doc.createElement('input');
            // input.setAttribute('type', 'checkbox');
            // input.setAttribute('disabled', 'disabled');
            //
            // if (this.hasAttribute('selected')) {
            //     input.setAttribute('selected', 'selected');
            // }
            //
            // this.shadowRoot.append(input);
            // this.shadowRoot.close();
        }
    }

    win.customElements.define('task-list-item', TaskListElement);
}

/**
 * @param {Editor} editor
 * @param {String} url
 */
function register(editor, url) {

    // editor.on('NewBlock', ({ newBlock}) => {
    //     ensureElementHasCheckbox(newBlock);
    // });

    editor.on('PreInit', () => {

        defineTaskListCustomElement(editor);

        editor.parser.addNodeFilter('li', function(elms) {
            for (const elem of elms) {
                if (elem.attributes.map.class === 'task-list-item') {
                    replaceTaskListNode(elem);
                }
            }
        });

        // editor.serializer.addNodeFilter('li', function(elms) {
        //     for (const elem of elms) {
        //         if (elem.attributes.map.class === 'task-list-item') {
        //             ensureNodeHasCheckbox(elem);
        //         }
        //     }
        // });

    });

}

/**
 * @param {AstNode} node
 */
function replaceTaskListNode(node) {

    const taskListItem = new tinymce.html.Node.create('task-list-item', {
    });

    for (const child of node.children()) {
        if (node.name !== 'input') {
            taskListItem.append(child);
        }
    }

    node.replace(taskListItem);
}

// /**
//  * @param {Element} elem
//  */
// function ensureElementHasCheckbox(elem) {
//     const hasCheckbox = elem.querySelector(':scope > input[type="checkbox"]') !== null;
//     if (hasCheckbox) {
//         return;
//     }
//
//     const input = elem.ownerDocument.createElement('input');
//     input.setAttribute('type', 'checkbox');
//     input.setAttribute('disabled', 'disabled');
//     elem.prepend(input);
// }

/**
 * @param {AstNode} elem
 */
function ensureNodeHasCheckbox(elem) {
    // Stop if there's already an input
    if (elem.firstChild && elem.firstChild.name === 'input') {
        return;
    }

    const input = new tinymce.html.Node.create('input', {
        type: 'checkbox',
        disabled: 'disabled',
    });

    if (elem.firstChild) {
        elem.insert(input, elem.firstChild, true);
    } else {
        elem.append(input);
    }
}


/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}