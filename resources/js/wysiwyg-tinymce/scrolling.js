/**
 * @param {Editor} editor
 * @param {String} scrollId
 */
function scrollToText(editor, scrollId) {
    const element = editor.dom.get(encodeURIComponent(scrollId).replace(/!/g, '%21'));
    if (!element) {
        return;
    }

    // scroll the element into the view and put the cursor at the end.
    element.scrollIntoView();
    editor.selection.select(element, true);
    editor.selection.collapse(false);
    editor.focus();
}

/**
 * Scroll to a specific header of the given index, relative to all headers in the content.
 * @param {Editor} editor
 * @param {Number} index
 */
export function scrollToHeader(editor, index) {
    const headers = editor.dom.select('h1, h2, h3, h4, h5, h6');
    const targetHeader = headers[index];

    if (targetHeader) {
        targetHeader.scrollIntoView();
        editor.selection.select(targetHeader, true);
        editor.selection.collapse(false);
        editor.focus();
    }
}

/**
 * Scroll to a section dictated by the current URL query string, if present.
 * Used when directly editing a specific section of the page.
 * @param {Editor} editor
 */
export function scrollToQueryString(editor) {
    const queryParams = (new URL(window.location)).searchParams;
    const scrollId = queryParams.get('content-id');
    if (scrollId) {
        scrollToText(editor, scrollId);
    }
}
