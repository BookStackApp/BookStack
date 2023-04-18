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
