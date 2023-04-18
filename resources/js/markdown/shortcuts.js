/**
 * Provide shortcuts for the editor instance.
 * @param {MarkdownEditor} editor
 * @returns {Object<String, Function>}
 */
function provide(editor) {
    const shortcuts = {};

    // Insert Image shortcut
    shortcuts['Shift-Mod-i'] = cm => editor.actions.insertImage();

    // Save draft
    shortcuts['Mod-s'] = cm => window.$events.emit('editor-save-draft');

    // Save page
    shortcuts['Mod-Enter'] = cm => window.$events.emit('editor-save-page');

    // Show link selector
    shortcuts['Shift-Mod-k'] = cm => editor.actions.showLinkSelector();

    // Insert Link
    shortcuts['Mod-k'] = cm => editor.actions.insertLink();

    // FormatShortcuts
    shortcuts['Mod-1'] = cm => editor.actions.replaceLineStart('##');
    shortcuts['Mod-2'] = cm => editor.actions.replaceLineStart('###');
    shortcuts['Mod-3'] = cm => editor.actions.replaceLineStart('####');
    shortcuts['Mod-4'] = cm => editor.actions.replaceLineStart('#####');
    shortcuts['Mod-5'] = cm => editor.actions.replaceLineStart('');
    shortcuts['Mod-d'] = cm => editor.actions.replaceLineStart('');
    shortcuts['Mod-6'] = cm => editor.actions.replaceLineStart('>');
    shortcuts['Mod-q'] = cm => editor.actions.replaceLineStart('>');
    shortcuts['Mod-7'] = cm => editor.actions.wrapSelection('\n```\n', '\n```');
    shortcuts['Mod-8'] = cm => editor.actions.wrapSelection('`', '`');
    shortcuts['Shift-Mod-e'] = cm => editor.actions.wrapSelection('`', '`');
    shortcuts['Mod-9'] = cm => editor.actions.cycleCalloutTypeAtSelection();
    shortcuts['Mod-p'] = cm => editor.actions.replaceLineStart('-')
    shortcuts['Mod-o'] = cm => editor.actions.replaceLineStartForOrderedList()

    return shortcuts;
}

/**
 * Get the editor shortcuts in CodeMirror keybinding format.
 * @param {MarkdownEditor} editor
 * @return {{key: String, run: function, preventDefault: boolean}[]}
 */
export function provideKeyBindings(editor) {
    const shortcuts= provide(editor);
    const keyBindings = [];

    const wrapAction = (action) => {
        return () => {
            action();
            return true;
        };
    };

    for (const [shortcut, action] of Object.entries(shortcuts)) {
        keyBindings.push({key: shortcut, run: wrapAction(action), preventDefault: true});
    }

    return keyBindings;
}