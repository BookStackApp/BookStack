function getContentToInsert({html, markdown}) {
    return markdown || html;
}

/**
 * @param {MarkdownEditor} editor
 */
export function listen(editor) {
    window.$events.listen('editor::replace', eventContent => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.replaceContent(markdown);
    });

    window.$events.listen('editor::append', eventContent => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.appendContent(markdown);
    });

    window.$events.listen('editor::prepend', eventContent => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.prependContent(markdown);
    });

    window.$events.listen('editor::insert', eventContent => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.insertContent(markdown);
    });

    window.$events.listen('editor::focus', () => {
        editor.actions.focus();
    });
}
