/**
 * @param {Editor} editor
 */
export function listen(editor) {

    // Replace editor content
    window.$events.listen('editor::replace', ({html}) => {
        editor.setContent(html);
    });

    // Append editor content
    window.$events.listen('editor::append', ({html}) => {
        const content = editor.getContent() + html;
        editor.setContent(content);
    });

    // Prepend editor content
    window.$events.listen('editor::prepend', ({html}) => {
        const content = html + editor.getContent();
        editor.setContent(content);
    });

    // Insert editor content at the current location
    window.$events.listen('editor::insert', ({html}) => {
        editor.insertContent(html);
    });

    // Focus on the editor
    window.$events.listen('editor::focus', () => {
        editor.focus();
    });
}