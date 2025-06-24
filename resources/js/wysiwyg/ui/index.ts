import {LexicalEditor} from "lexical";
import {EditorUIManager} from "./framework/manager";
import {EditorUiContext} from "./framework/core";
import {el} from "../utils/dom";

export function buildEditorUI(containerDOM: HTMLElement, editor: LexicalEditor, options: Record<string, any>): EditorUiContext {
    const editorDOM = el('div', {
        contenteditable: 'true',
        class: `editor-content-area ${options.editorClass || ''}`,
    });
    const scrollDOM = el('div', {
        class: 'editor-content-wrap',
    }, [editorDOM]);

    containerDOM.append(scrollDOM);
    containerDOM.classList.add('editor-container');
    containerDOM.setAttribute('dir', options.textDirection);
    if (options.darkMode) {
        containerDOM.classList.add('editor-dark');
    }

    const manager = new EditorUIManager();
    const context: EditorUiContext = {
        editor,
        containerDOM: containerDOM,
        editorDOM: editorDOM,
        scrollDOM: scrollDOM,
        manager,
        translate(text: string): string {
            const translations = options.translations;
            return translations[text] || text;
        },
        error(error: string|Error): void {
            const message = error instanceof Error ? error.message : error;
            window.$events.error(message); // TODO - Translate
        },
        options,
    };
    manager.setContext(context);

    return context;
}