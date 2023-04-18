/**
 * @param {Editor} editor
 * @param {String} url
 */
function register(editor, url) {
    // Tasklist UI buttons
    editor.ui.registry.addIcon('tasklist', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M22,8c0-0.55-0.45-1-1-1h-7c-0.55,0-1,0.45-1,1s0.45,1,1,1h7C21.55,9,22,8.55,22,8z M13,16c0,0.55,0.45,1,1,1h7 c0.55,0,1-0.45,1-1c0-0.55-0.45-1-1-1h-7C13.45,15,13,15.45,13,16z M10.47,4.63c0.39,0.39,0.39,1.02,0,1.41l-4.23,4.25 c-0.39,0.39-1.02,0.39-1.42,0L2.7,8.16c-0.39-0.39-0.39-1.02,0-1.41c0.39-0.39,1.02-0.39,1.41,0l1.42,1.42l3.54-3.54 C9.45,4.25,10.09,4.25,10.47,4.63z M10.48,12.64c0.39,0.39,0.39,1.02,0,1.41l-4.23,4.25c-0.39,0.39-1.02,0.39-1.42,0L2.7,16.16 c-0.39-0.39-0.39-1.02,0-1.41s1.02-0.39,1.41,0l1.42,1.42l3.54-3.54C9.45,12.25,10.09,12.25,10.48,12.64L10.48,12.64z"/></svg>');
    editor.ui.registry.addToggleButton('tasklist', {
        tooltip: 'Task list',
        icon: 'tasklist',
        active: false,
        onAction(api) {
            if (api.isActive()) {
                editor.execCommand('RemoveList');
            } else {
                editor.execCommand('InsertUnorderedList', null, {
                    'list-item-attributes': {
                        class: 'task-list-item',
                    },
                    'list-style-type': 'tasklist',
                });
            }
        },
        onSetup(api) {
            editor.on('NodeChange', event => {
                const parentListEl = event.parents.find(el => el.nodeName === 'LI');
                const inList = parentListEl && parentListEl.classList.contains('task-list-item');
                api.setActive(Boolean(inList));
            });
        },
    });

    // Tweak existing bullet list button active state to not be active
    // when we're in a task list.
    const existingBullListButton = editor.ui.registry.getAll().buttons.bullist;
    existingBullListButton.onSetup = function(api) {
        editor.on('NodeChange', event => {
            const parentList = event.parents.find(el => el.nodeName === 'LI');
            const inTaskList = parentList && parentList.classList.contains('task-list-item');
            const inUlList = parentList && parentList.parentNode.nodeName === 'UL';
            api.setActive(Boolean(inUlList && !inTaskList));
        });
    };
    existingBullListButton.onAction = function() {
        // Cheeky hack to prevent list toggle action treating tasklists as normal
        // unordered lists which would unwrap the list on toggle from tasklist to bullet list.
        // Instead we quickly jump through an ordered list first if we're within a tasklist.
        if (elementWithinTaskList(editor.selection.getNode())) {
            editor.execCommand('InsertOrderedList', null, {
                'list-item-attributes': {class: null},
            });
        }

        editor.execCommand('InsertUnorderedList', null, {
            'list-item-attributes': {class: null},
        });
    };
    // Tweak existing number list to not allow classes on child items
    const existingNumListButton = editor.ui.registry.getAll().buttons.numlist;
    existingNumListButton.onAction = function() {
        editor.execCommand('InsertOrderedList', null, {
            'list-item-attributes': {class: null},
        });
    };

    // Setup filters on pre-init
    editor.on('PreInit', () => {
        editor.parser.addNodeFilter('li', nodes => {
            for (const node of nodes) {
                if (node.attributes.map.class === 'task-list-item') {
                    parseTaskListNode(node);
                }
            }
        });
        editor.serializer.addNodeFilter('li', nodes => {
            for (const node of nodes) {
                if (node.attributes.map.class === 'task-list-item') {
                    serializeTaskListNode(node);
                }
            }
        });
    });

    // Handle checkbox click in editor
    editor.on('click', event => {
        const clickedEl = event.target;
        if (clickedEl.nodeName === 'LI' && clickedEl.classList.contains('task-list-item')) {
            handleTaskListItemClick(event, clickedEl, editor);
            event.preventDefault();
        }
    });
}

/**
 * @param {Element} element
 * @return {boolean}
 */
function elementWithinTaskList(element) {
    const listEl = element.closest('li');
    return listEl && listEl.parentNode.nodeName === 'UL' && listEl.classList.contains('task-list-item');
}

/**
 * @param {MouseEvent} event
 * @param {Element} clickedEl
 * @param {Editor} editor
 */
function handleTaskListItemClick(event, clickedEl, editor) {
    const bounds = clickedEl.getBoundingClientRect();
    const withinBounds = event.clientX <= bounds.right
                        && event.clientX >= bounds.left
                        && event.clientY >= bounds.top
                        && event.clientY <= bounds.bottom;

    // Outside of the task list item bounds mean we're probably clicking the pseudo-element.
    if (!withinBounds) {
        editor.undoManager.transact(() => {
            if (clickedEl.hasAttribute('checked')) {
                clickedEl.removeAttribute('checked');
            } else {
                clickedEl.setAttribute('checked', 'checked');
            }
        });
    }
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
    // Get checked status and clean it from list node
    const isChecked = node.attr('checked') === 'checked';
    node.attr('checked', null);

    const inputAttrs = {type: 'checkbox', disabled: 'disabled'};
    if (isChecked) {
        inputAttrs.checked = 'checked';
    }

    // Create & insert checkbox input element
    const checkbox = tinymce.html.Node.create('input', inputAttrs);
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
