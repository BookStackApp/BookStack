import {provideKeyBindings} from './shortcuts';
import {EditorView, ViewUpdate} from "@codemirror/view";
import {MarkdownEditor} from "./index.mjs";
import {CodeModule} from "../global";
import {MarkdownEditorEventMap} from "./dom-handlers";

/**
 * Initiate the codemirror instance for the Markdown editor.
 */
export function init(editor: MarkdownEditor, Code: CodeModule, domEventHandlers: MarkdownEditorEventMap): EditorView {
    function onViewUpdate(v: ViewUpdate) {
        if (v.docChanged) {
            editor.actions.updateAndRender();
        }
    }


    const cm = Code.markdownEditor(
        editor.config.inputEl,
        onViewUpdate,
        domEventHandlers,
        provideKeyBindings(editor),
    );

    // Add editor view to the window for easy access/debugging.
    // Not part of official API/Docs
    // @ts-ignore
    window.mdEditorView = cm;

    return cm;
}
