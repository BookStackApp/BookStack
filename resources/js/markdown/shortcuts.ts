import {MarkdownEditor} from "./index.mjs";

export type MarkdownEditorShortcutMap = Record<string, () => void>;

/**
 * Provide shortcuts for the editor instance.
 */
export function provideShortcutMap(editor: MarkdownEditor): MarkdownEditorShortcutMap {
    const shortcuts: MarkdownEditorShortcutMap = {};

    // Insert Image shortcut
    shortcuts['Shift-Mod-i'] = () => editor.actions.insertImage();

    // Save draft
    shortcuts['Mod-s'] = () => window.$events.emit('editor-save-draft');

    // Save page
    shortcuts['Mod-Enter'] = () => window.$events.emit('editor-save-page');

    // Show link selector
    shortcuts['Shift-Mod-k'] = () => editor.actions.showLinkSelector();

    // Insert Link
    shortcuts['Mod-k'] = () => editor.actions.insertLink();

    // FormatShortcuts
    shortcuts['Mod-1'] = () => editor.actions.replaceLineStart('##');
    shortcuts['Mod-2'] = () => editor.actions.replaceLineStart('###');
    shortcuts['Mod-3'] = () => editor.actions.replaceLineStart('####');
    shortcuts['Mod-4'] = () => editor.actions.replaceLineStart('#####');
    shortcuts['Mod-5'] = () => editor.actions.replaceLineStart('');
    shortcuts['Mod-d'] = () => editor.actions.replaceLineStart('');
    shortcuts['Mod-6'] = () => editor.actions.replaceLineStart('>');
    shortcuts['Mod-q'] = () => editor.actions.replaceLineStart('>');
    shortcuts['Mod-7'] = () => editor.actions.wrapSelection('\n```\n', '\n```');
    shortcuts['Mod-8'] = () => editor.actions.wrapSelection('`', '`');
    shortcuts['Shift-Mod-e'] = () => editor.actions.wrapSelection('`', '`');
    shortcuts['Mod-9'] = () => editor.actions.cycleCalloutTypeAtSelection();
    shortcuts['Mod-p'] = () => editor.actions.replaceLineStart('-');
    shortcuts['Mod-o'] = () => editor.actions.replaceLineStartForOrderedList();

    return shortcuts;
}
