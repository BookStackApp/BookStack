/**
 * @param {Editor} editor
 * @param {String} url
 */

function register(editor, url) {

    editor.ui.registry.addIcon('details', '<svg width="24" height="24"><path d="M8.2 9a.5.5 0 0 0-.4.8l4 5.6a.5.5 0 0 0 .8 0l4-5.6a.5.5 0 0 0-.4-.8ZM20.122 18.151h-16c-.964 0-.934 2.7 0 2.7h16c1.139 0 1.173-2.7 0-2.7zM20.122 3.042h-16c-.964 0-.934 2.7 0 2.7h16c1.139 0 1.173-2.7 0-2.7z"/></svg>');
    editor.ui.registry.addIcon('togglefold', '<svg height="24"  width="24"><path d="M8.12 19.3c.39.39 1.02.39 1.41 0L12 16.83l2.47 2.47c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41l-3.17-3.17c-.39-.39-1.02-.39-1.41 0l-3.17 3.17c-.4.38-.4 1.02-.01 1.41zm7.76-14.6c-.39-.39-1.02-.39-1.41 0L12 7.17 9.53 4.7c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.03 0 1.42l3.17 3.17c.39.39 1.02.39 1.41 0l3.17-3.17c.4-.39.4-1.03.01-1.42z"/></svg>');
    editor.ui.registry.addIcon('togglelabel', '<svg height="18" width="18" viewBox="0 0 24 24"><path d="M21.41,11.41l-8.83-8.83C12.21,2.21,11.7,2,11.17,2H4C2.9,2,2,2.9,2,4v7.17c0,0.53,0.21,1.04,0.59,1.41l8.83,8.83 c0.78,0.78,2.05,0.78,2.83,0l7.17-7.17C22.2,13.46,22.2,12.2,21.41,11.41z M6.5,8C5.67,8,5,7.33,5,6.5S5.67,5,6.5,5S8,5.67,8,6.5 S7.33,8,6.5,8z"/></svg>');

    editor.ui.registry.addButton('details', {
        icon: 'details',
        tooltip: 'Insert collapsible block',
        onAction() {
            editor.execCommand('InsertDetailsBlock');
        }
    });

    editor.ui.registry.addButton('removedetails', {
        icon: 'table-delete-table',
        tooltip: 'Unwrap',
        onAction() {
            unwrapDetailsInSelection(editor)
        }
    });

    editor.ui.registry.addButton('editdetials', {
        icon: 'togglelabel',
        tooltip: 'Edit label',
        onAction() {
            showDetailLabelEditWindow(editor);
        }
    });

    editor.on('dblclick', event => {
        if (!getSelectedDetailsBlock(editor) || event.target.closest('doc-root')) return;
        showDetailLabelEditWindow(editor);
    });

    editor.ui.registry.addButton('toggledetails', {
        icon: 'togglefold',
        tooltip: 'Toggle open/closed',
        onAction() {
            const details = getSelectedDetailsBlock(editor);
            details.toggleAttribute('open');
            editor.focus();
        }
    });

    editor.addCommand('InsertDetailsBlock', function () {
        let content = editor.selection.getContent({format: 'html'});
        const details = document.createElement('details');
        const summary = document.createElement('summary');
        const id = 'details-' + Date.now();
        details.setAttribute('data-id', id)
        details.appendChild(summary);

        if (!content) {
            content = '<p><br></p>';
        }

        details.innerHTML += content;
        editor.insertContent(details.outerHTML);
        editor.focus();

        const domDetails = editor.dom.$(`[data-id="${id}"]`);
        if (domDetails) {
            const firstChild = domDetails.find('doc-root > *');
            if (firstChild) {
                firstChild[0].focus();
            }
            domDetails.removeAttr('data-id');
        }
    });

    editor.ui.registry.addContextToolbar('details', {
        predicate: function (node) {
            return node.nodeName.toLowerCase() === 'details';
        },
        items: 'editdetials toggledetails removedetails',
        position: 'node',
        scope: 'node'
    });

    editor.on('PreInit', () => {
        setupElementFilters(editor);
    });
}

/**
 * @param {Editor} editor
 */
function showDetailLabelEditWindow(editor) {
    const details = getSelectedDetailsBlock(editor);
    const dialog = editor.windowManager.open(detailsDialog(editor));
    dialog.setData({summary: getSummaryTextFromDetails(details)});
}

/**
 * @param {Editor} editor
 */
function getSelectedDetailsBlock(editor) {
    return editor.selection.getNode().closest('details');
}

/**
 * @param {Element} element
 */
function getSummaryTextFromDetails(element) {
    const summary = element.querySelector('summary');
    if (!summary) {
        return '';
    }
    return summary.textContent;
}

/**
 * @param {Editor} editor
 */
function detailsDialog(editor) {
    return {
        title: 'Edit collapsible block',
        body: {
            type: 'panel',
            items: [
                {
                    type: 'input',
                    name: 'summary',
                    label: 'Toggle label',
                },
            ],
        },
        buttons: [
            {
                type: 'cancel',
                text: 'Cancel'
            },
            {
                type: 'submit',
                text: 'Save',
                primary: true,
            }
        ],
        onSubmit(api) {
            const {summary} = api.getData();
            setSummary(editor, summary);
            api.close();
        }
    }
}

function setSummary(editor, summaryContent) {
    const details = getSelectedDetailsBlock(editor);
    if (!details) return;

    editor.undoManager.transact(() => {
        let summary = details.querySelector('summary');
        if (!summary) {
            summary = document.createElement('summary');
            details.prepend(summary);
        }
        summary.textContent = summaryContent;
    });
}

/**
 * @param {Editor} editor
 */
function unwrapDetailsInSelection(editor) {
    const details = editor.selection.getNode().closest('details');

    if (details) {
        const elements = details.querySelectorAll('details > *:not(summary, doc-root), doc-root > *');

        editor.undoManager.transact(() => {
            for (const element of elements) {
                details.parentNode.insertBefore(element, details);
            }
            details.remove();
        });
    }
    editor.focus();
}

/**
 * @param {Editor} editor
 */
function setupElementFilters(editor) {
    editor.parser.addNodeFilter('details', function(elms) {
        for (const el of elms) {
            ensureDetailsWrappedInEditable(el);
        }
    });

    editor.serializer.addNodeFilter('details', function(elms) {
        for (const el of elms) {
            unwrapDetailsEditable(el);
            el.attr('open', null);
        }
    });

    editor.serializer.addNodeFilter('doc-root', function(elms) {
        for (const el of elms) {
            el.unwrap();
        }
    });
}

/**
 * @param {tinymce.html.Node} detailsEl
 */
function ensureDetailsWrappedInEditable(detailsEl) {
    unwrapDetailsEditable(detailsEl);

    detailsEl.attr('contenteditable', 'false');
    const wrap = tinymce.html.Node.create('doc-root', {contenteditable: 'true'});
    for (const child of detailsEl.children()) {
        if (child.name !== 'summary') {
            wrap.append(child);
        }
    }

    detailsEl.append(wrap);
}

/**
 * @param {tinymce.html.Node} detailsEl
 */
function unwrapDetailsEditable(detailsEl) {
    detailsEl.attr('contenteditable', null);
    let madeUnwrap = false;
    for (const child of detailsEl.children()) {
        if (child.name === 'doc-root') {
            child.unwrap();
            madeUnwrap = true;
        }
    }

    if (madeUnwrap) {
        unwrapDetailsEditable(detailsEl);
    }
}


/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}