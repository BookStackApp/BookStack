import {Markdown} from './markdown';
import {Display} from './display';
import {Actions} from './actions';
import {Settings} from './settings';
import {listenToCommonEvents} from './common-events';
import {init as initCodemirror} from './codemirror';
import {CodeModule} from "../global";
import {MarkdownEditorInput} from "./inputs/interface";
import {CodemirrorInput} from "./inputs/codemirror";
import {TextareaInput} from "./inputs/textarea";
import {provideShortcutMap} from "./shortcuts";
import {getMarkdownDomEventHandlers} from "./dom-handlers";

export interface MarkdownEditorConfig {
    pageId: string;
    container: Element;
    displayEl: HTMLIFrameElement;
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
    input: MarkdownEditorInput;
    settings: Settings;
}

/**
 * Initiate a new Markdown editor instance.
 */
export async function init(config: MarkdownEditorConfig): Promise<MarkdownEditor> {
    // const Code = await window.importVersioned('code') as CodeModule;

    const editor: MarkdownEditor = {
        config,
        markdown: new Markdown(),
        settings: new Settings(config.settingInputs),
    } as MarkdownEditor;

    editor.actions = new Actions(editor);
    editor.display = new Display(editor);

    const eventHandlers = getMarkdownDomEventHandlers(editor);
    // TODO - Switching
    // const codeMirror = initCodemirror(editor, Code);
    // editor.input = new CodemirrorInput(codeMirror);
    editor.input = new TextareaInput(
        config.inputEl,
        provideShortcutMap(editor),
        eventHandlers
    );

    // window.devinput = editor.input;

    listenToCommonEvents(editor);

    return editor;
}


