/**
 * Provide shortcuts for the given codemirror instance.
 * @param {MarkdownEditor} editor
 * @param {String} metaKey
 * @returns {Object<String, Function>}
 */
export function provide(editor, metaKey) {
    const shortcuts = {};

    // Insert Image shortcut
    shortcuts[`${metaKey}-Alt-I`] = function(cm) {
        const selectedText = cm.getSelection();
        const newText = `![${selectedText}](http://)`;
        const cursorPos = cm.getCursor('from');
        cm.replaceSelection(newText);
        cm.setCursor(cursorPos.line, cursorPos.ch + newText.length -1);
    };

    // Save draft
    shortcuts[`${metaKey}-S`] = cm => window.$events.emit('editor-save-draft');

    // Save page
    shortcuts[`${metaKey}-Enter`] = cm => window.$events.emit('editor-save-page');

    // Show link selector
    shortcuts[`Shift-${metaKey}-K`] = cm => editor.actions.showLinkSelector();

    // Insert Link
    shortcuts[`${metaKey}-K`] = cm => editor.actions.insertLink();

    // FormatShortcuts
    shortcuts[`${metaKey}-1`] = cm => editor.actions.replaceLineStart('##');
    shortcuts[`${metaKey}-2`] = cm => editor.actions.replaceLineStart('###');
    shortcuts[`${metaKey}-3`] = cm => editor.actions.replaceLineStart('####');
    shortcuts[`${metaKey}-4`] = cm => editor.actions.replaceLineStart('#####');
    shortcuts[`${metaKey}-5`] = cm => editor.actions.replaceLineStart('');
    shortcuts[`${metaKey}-D`] = cm => editor.actions.replaceLineStart('');
    shortcuts[`${metaKey}-6`] = cm => editor.actions.replaceLineStart('>');
    shortcuts[`${metaKey}-Q`] = cm => editor.actions.replaceLineStart('>');
    shortcuts[`${metaKey}-7`] = cm => editor.actions.wrapSelection('\n```\n', '\n```');
    shortcuts[`${metaKey}-8`] = cm => editor.actions.wrapSelection('`', '`');
    shortcuts[`Shift-${metaKey}-E`] = cm => editor.actions.wrapSelection('`', '`');
    shortcuts[`${metaKey}-9`] = cm => editor.actions.cycleCalloutTypeAtSelection();
    shortcuts[`${metaKey}-P`] = cm => editor.actions.replaceLineStart('-')
    shortcuts[`${metaKey}-O`] = cm => editor.actions.replaceLineStartForOrderedList()

    return shortcuts;
}