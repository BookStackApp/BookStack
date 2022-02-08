/**
 * @param {Editor} editor
 * @param {String} url
 */

function register(editor, url) {

    editor.ui.registry.addIcon('details', '<svg width="24" height="24"><path d="M8.2 9a.5.5 0 0 0-.4.8l4 5.6a.5.5 0 0 0 .8 0l4-5.6a.5.5 0 0 0-.4-.8ZM20.122 18.151h-16c-.964 0-.934 2.7 0 2.7h16c1.139 0 1.173-2.7 0-2.7zM20.122 3.042h-16c-.964 0-.934 2.7 0 2.7h16c1.139 0 1.173-2.7 0-2.7z"/></svg>');

    editor.ui.registry.addButton('details', {
        icon: 'details',
        tooltip: 'Insert collapsible block',
        onAction() {
            editor.execCommand('InsertDetailsBlock');
        }
    });

    editor.ui.registry.addButton('removedetails', {
        icon: 'table-delete-table',
        tooltip: 'Unwrap collapsible block',
        onAction() {
            unwrapDetailsInSelection(editor)
        }
    });

    editor.ui.registry.addButton('editdetials', {
        icon: 'tag',
        tooltip: 'Edit label',
        onAction() {
            const details = getSelectedDetailsBlock(editor);
            const dialog = editor.windowManager.open(detailsDialog(editor));
            dialog.setData({summary: getSummaryTextFromDetails(details)});
        }
    });

    editor.ui.registry.addButton('collapsedetails', {
        icon: 'action-prev',
        tooltip: 'Collapse',
        onAction() {
            const details = getSelectedDetailsBlock(editor);
            details.removeAttribute('open');
            editor.focus();
        }
    });

    editor.ui.registry.addButton('expanddetails', {
        icon: 'action-next',
        tooltip: 'Expand',
        onAction() {
            const details = getSelectedDetailsBlock(editor);
            details.setAttribute('open', 'open');
            editor.focus();
        }
    });

    editor.addCommand('InsertDetailsBlock', function () {
        const content = editor.selection.getContent({format: 'html'});
        const details = document.createElement('details');
        const summary = document.createElement('summary');
        details.appendChild(summary);
        details.innerHTML += content;

        editor.insertContent(details.outerHTML);
    });

    editor.ui.registry.addContextToolbar('details', {
        predicate: function (node) {
            return node.nodeName.toLowerCase() === 'details';
        },
        items: 'removedetails editdetials collapsedetails expanddetails',
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
                    label: 'Toggle label text',
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
            details.appendChild(summary);
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
        const summary = details.querySelector('summary');
        editor.undoManager.transact(() => {
            if (summary) {
                summary.remove();
            }
            while (details.firstChild) {
                details.parentNode.insertBefore(details.firstChild, details);
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
            // el.attr('contenteditable', 'false');
            // console.log(el);
            // let wrap = el.find('div[detailswrap]');
            // if (!wrap) {
            //    wrap = document.createElement('div');
            //    wrap.setAttribute('detailswrap', 'true');
            // }
            //
            // for (const child of el.children) {
            //     if (child.nodeName.toLowerCase() === 'summary' || child.hasAttribute('detailswrap')) {
            //         continue;
            //     }
            //     wrap.appendChild(child);
            // }
            //
            // el.appendChild(wrap);
            // wrap.setAttribute('contenteditable', 'true');
        }
    });

    editor.serializer.addNodeFilter('details', function(elms) {
        for (const summaryEl of elms) {
            summaryEl.attr('contenteditable', null);
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