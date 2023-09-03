/**
 * Setup a serializer filter for <br> tags to ensure they're not rendered
 * within code blocks and that we use newlines there instead.
 * @param {Editor} editor
 */
function setupBrFilter(editor) {
    editor.serializer.addNodeFilter('br', nodes => {
        for (const node of nodes) {
            if (node.parent && node.parent.name === 'code') {
                const newline = window.tinymce.html.Node.create('#text');
                newline.value = '\n';
                node.replace(newline);
            }
        }
    });
}

/**
 * Remove accidentally added pointer elements that are within the content.
 * These could have accidentally been added via getting caught in range
 * selection within page content.
 * @param {Editor} editor
 */
function setupPointerFilter(editor) {
    editor.parser.addNodeFilter('div', nodes => {
        for (const node of nodes) {
            if (node.attr('id') === 'pointer' || node.attr('class').includes('pointer')) {
                node.remove();
            }
        }
    });
}

/**
 * Setup global default filters for the given editor instance.
 * @param {Editor} editor
 */
export function setupFilters(editor) {
    setupBrFilter(editor);
    setupPointerFilter(editor);
}
