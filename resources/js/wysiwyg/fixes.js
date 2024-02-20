/**
 * Handle alignment for embed (iframe/video) content.
 * TinyMCE built-in handling doesn't work well for these when classes are used for
 * alignment, since the editor wraps these elements in a non-editable preview span
 * which looses tracking and setting of alignment options.
 * Here we manually manage these properties and formatting events, by effectively
 * syncing the alignment classes to the parent preview span.
 * @param {Editor} editor
 */
export function handleEmbedAlignmentChanges(editor) {
    function updateClassesForPreview(previewElem) {
        const mediaTarget = previewElem.querySelector('iframe, video');
        if (!mediaTarget) {
            return;
        }

        const alignmentClasses = [...mediaTarget.classList.values()].filter(c => c.startsWith('align-'));
        const previewAlignClasses = [...previewElem.classList.values()].filter(c => c.startsWith('align-'));
        previewElem.classList.remove(...previewAlignClasses);
        previewElem.classList.add(...alignmentClasses);
    }

    editor.on('SetContent', () => {
        const previewElems = editor.dom.select('span.mce-preview-object');
        for (const previewElem of previewElems) {
            updateClassesForPreview(previewElem);
        }
    });

    editor.on('FormatApply', event => {
        const isAlignment = event.format.startsWith('align');
        if (!event.node || !event.node.matches('.mce-preview-object')) {
            return;
        }

        const realTarget = event.node.querySelector('iframe, video');
        if (isAlignment && realTarget) {
            const className = (editor.formatter.get(event.format)[0]?.classes || [])[0];
            const toAdd = !realTarget.classList.contains(className);

            const wrapperClasses = (event.node.getAttribute('data-mce-p-class') || '').split(' ');
            const wrapperClassesFiltered = wrapperClasses.filter(c => !c.startsWith('align-'));
            if (toAdd) {
                wrapperClassesFiltered.push(className);
            }

            const classesToApply = wrapperClassesFiltered.join(' ');
            event.node.setAttribute('data-mce-p-class', classesToApply);

            realTarget.setAttribute('class', classesToApply);
            editor.formatter.apply(event.format, {}, realTarget);
            updateClassesForPreview(event.node);
        }
    });
}

/**
 * Cleans up the direction property for an element.
 * Removes all inline direction control from child elements.
 * Removes non "dir" attribute direction control from provided element.
 * @param {HTMLElement} element
 */
function cleanElementDirection(element) {
    const directionChildren = element.querySelectorAll('[dir],[style*="direction"],[style*="text-align"]');
    for (const child of directionChildren) {
        child.removeAttribute('dir');
        child.style.direction = null;
        child.style.textAlign = null;
    }
    element.style.direction = null;
    element.style.textAlign = null;
}

/**
 * This tracks table cell range selection, so we can apply custom handling where
 * required to actions applied to such selections.
 * The events used don't seem to be advertised by TinyMCE.
 * Found at https://github.com/tinymce/tinymce/blob/6.8.3/modules/tinymce/src/models/dom/main/ts/table/api/Events.ts
 * @param {Editor} editor
 */
export function handleTableCellRangeEvents(editor) {
    /** @var {HTMLTableCellElement[]} * */
    let selectedCells = [];

    editor.on('TableSelectionChange', event => {
        selectedCells = (event.cells || []).map(cell => cell.dom);
    });
    editor.on('TableSelectionClear', () => {
        selectedCells = [];
    });

    // TinyMCE does not seem to do a great job on clearing styles in complex
    // scenarios (like copied word content) when a range of table cells
    // are selected. Here we watch for clear formatting events, so some manual
    // cleanup can be performed.
    const attrsToRemove = ['class', 'style', 'width', 'height'];
    editor.on('FormatRemove', () => {
        for (const cell of selectedCells) {
            for (const attr of attrsToRemove) {
                cell.removeAttribute(attr);
            }
        }
    });

    // TinyMCE does not apply direction events to table cell range selections
    // so here we hastily patch in that ability by setting the direction ourselves
    // when a direction event is fired.
    editor.on('ExecCommand', event => {
        const command = event.command;
        if (command !== 'mceDirectionLTR' && command !== 'mceDirectionRTL') {
            return;
        }

        const dir = command === 'mceDirectionLTR' ? 'ltr' : 'rtl';
        for (const cell of selectedCells) {
            cell.setAttribute('dir', dir);
            cleanElementDirection(cell);
        }
    });
}

/**
 * Direction control might not work if there are other unexpected direction-handling styles
 * or attributes involved nearby. This watches for direction change events to clean
 * up direction controls, removing non-dir-attr direction controls, while removing
 * directions from child elements that may be involved.
 * @param {Editor} editor
 */
export function handleTextDirectionCleaning(editor) {
    editor.on('ExecCommand', event => {
        const command = event.command;
        if (command !== 'mceDirectionLTR' && command !== 'mceDirectionRTL') {
            return;
        }

        const blocks = editor.selection.getSelectedBlocks();
        for (const block of blocks) {
            cleanElementDirection(block);
        }
    });
}
