import {EditorView, KeyBinding, ViewUpdate} from "@codemirror/view";
import {CodeModule} from "../global";
import {MarkdownEditorEventMap} from "./dom-handlers";
import {MarkdownEditorShortcutMap} from "./shortcuts";

/**
 * Convert editor shortcuts to CodeMirror keybinding format.
 */
export function shortcutsToKeyBindings(shortcuts: MarkdownEditorShortcutMap): KeyBinding[] {
    const keyBindings = [];

    const wrapAction = (action: () => void) => () => {
        action();
        return true;
    };

    for (const [shortcut, action] of Object.entries(shortcuts)) {
        keyBindings.push({key: shortcut, run: wrapAction(action), preventDefault: true});
    }

    return keyBindings;
}

/**
 * Initiate the codemirror instance for the Markdown editor.
 */
export async function init(
    input: HTMLTextAreaElement,
    shortcuts: MarkdownEditorShortcutMap,
    domEventHandlers: MarkdownEditorEventMap,
    onChange: () => void
): Promise<EditorView> {
    const Code = await window.importVersioned('code') as CodeModule;

    function onViewUpdate(v: ViewUpdate) {
        if (v.docChanged) {
            onChange();
        }
    }

    const cm = Code.markdownEditor(
        input,
        onViewUpdate,
        domEventHandlers,
        shortcutsToKeyBindings(shortcuts),
    );

    // Add editor view to the window for easy access/debugging.
    // Not part of official API/Docs
    // @ts-ignore
    window.mdEditorView = cm;

    return cm;
}
