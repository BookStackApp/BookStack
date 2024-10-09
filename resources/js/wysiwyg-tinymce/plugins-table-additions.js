/**
 * @param {Editor} editor
 */
function register(editor) {
    editor.ui.registry.addIcon('tableclearformatting', '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 24 24"><path d="M15.53088 4.64727v-.82364c0-.453-.37063-.82363-.82363-.82363H4.82363C4.37063 3 4 3.37064 4 3.82363v3.29454c0 .453.37064.82364.82363.82364h9.88362c.453 0 .82363-.37064.82363-.82364v-.82363h.82364v3.29454H8.11817v7.4127c0 .453.37064.82364.82364.82364h1.64727c.453 0 .82363-.37064.82363-.82364v-5.76544h6.58907V4.64727Z"/><path d="m18.42672 19.51563-1.54687-1.54688-1.54688 1.54688c-.26751.2675-.70124.2675-.96875 0-.26751-.26752-.26751-.70124 0-.96876L15.9111 17l-1.54688-1.54688c-.26751-.2675-.26751-.70123 0-.96875.26751-.2675.70124-.2675.96875 0l1.54688 1.54688 1.54687-1.54688c.26751-.2675.70124-.2675.96875 0 .26751.26752.26751.70124 0 .96875L17.8486 17l1.54687 1.54688c.26751.2675.26751.70123 0 .96874-.26751.26752-.70124.26752-.96875 0z"/></svg>');

    const tableFirstRowContextSpec = {
        items: ' | tablerowheader',
        predicate(elem) {
            const isTable = elem.nodeName.toLowerCase() === 'table';
            const selectionNode = editor.selection.getNode();
            const parentTable = selectionNode.closest('table');
            if (!isTable || !parentTable) {
                return false;
            }

            const firstRow = parentTable.querySelector('tr');
            return firstRow.contains(selectionNode);
        },
        position: 'node',
        scope: 'node',
    };
    editor.ui.registry.addContextToolbar('customtabletoolbarfirstrow', tableFirstRowContextSpec);

    editor.addCommand('tableclearformatting', () => {
        const table = editor.dom.getParent(editor.selection.getStart(), 'table');
        if (!table) {
            return;
        }

        const attrsToRemove = ['class', 'style', 'width', 'height'];
        const styled = [table, ...table.querySelectorAll(attrsToRemove.map(a => `[${a}]`).join(','))];
        for (const elem of styled) {
            for (const attr of attrsToRemove) {
                elem.removeAttribute(attr);
            }
        }
    });

    editor.addCommand('tableclearsizes', () => {
        const table = editor.dom.getParent(editor.selection.getStart(), 'table');
        if (!table) {
            return;
        }

        const targets = [table, ...table.querySelectorAll('tr,td,th,tbody,thead,tfoot,th>*,td>*')];
        for (const elem of targets) {
            elem.removeAttribute('width');
            elem.removeAttribute('height');
            elem.style.height = null;
            elem.style.width = null;
        }
    });

    const onPreInit = () => {
        const exitingButtons = editor.ui.registry.getAll().buttons;

        editor.ui.registry.addMenuButton('customtable', {
            ...exitingButtons.table,
            fetch: callback => callback('inserttable | cell row column | advtablesort | tableprops tableclearformatting tableclearsizes deletetable'),
        });

        editor.ui.registry.addMenuItem('tableclearformatting', {
            icon: 'tableclearformatting',
            text: 'Clear table formatting',
            onSetup: exitingButtons.tableprops.onSetup,
            onAction() {
                editor.execCommand('tableclearformatting');
            },
        });

        editor.ui.registry.addMenuItem('tableclearsizes', {
            icon: 'resize',
            text: 'Resize to contents',
            onSetup: exitingButtons.tableprops.onSetup,
            onAction() {
                editor.execCommand('tableclearsizes');
            },
        });

        editor.off('PreInit', onPreInit);
    };

    editor.on('PreInit', onPreInit);
}

/**
 * @return {register}
 */
export function getPlugin() {
    return register;
}
