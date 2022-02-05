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
 * Override for touch events to allow scrolling on mobile devices.
 * TODO - Check if still needed or if needs editing.
 * @param {Editor} editor
 */
export function fixScrollForMobile(editor) {
    const container = editor.getContainer();
    const toolbarButtons = container.querySelectorAll('.mce-btn');
    for (let button of toolbarButtons) {
        button.addEventListener('touchstart', event => {
            event.stopPropagation();
        });
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