import {Markdown} from './markdown';
import {Display} from './display';
import {Actions} from './actions';
import {Settings} from './settings';
import {listenToCommonEvents} from './common-events';
import {init as initCodemirror} from './codemirror';
import {EditorView} from "@codemirror/view";

export interface MarkdownEditorConfig {
    pageId: string;
    container: Element;
    displayEl: Element;
    inputEl: HTMLTextAreaElement;
    drawioUrl: string;
    settingInputs: HTMLInputElement[];
    text: Record<string, string>;
}

export interface MarkdownEditor {
    config: MarkdownEditorConfig;
    display: Display;
    markdown: Markdown;
    actions: Actions;
    cm: EditorView;
    settings: Settings;
}

/**
 * Initiate a new Markdown editor instance.
 * @param {MarkdownEditorConfig} config
 * @returns {Promise<MarkdownEditor>}
 */
export async function init(config) {
    /**
     * @type {MarkdownEditor}
     */
    const editor: MarkdownEditor = {
        config,
        markdown: new Markdown(),
        settings: new Settings(config.settingInputs),
    };

    editor.actions = new Actions(editor);
    editor.display = new Display(editor);
    editor.cm = await initCodemirror(editor);

    listenToCommonEvents(editor);

    return editor;
}


