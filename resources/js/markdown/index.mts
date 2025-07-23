import {Markdown} from './markdown';
import {Display} from './display';
import {Actions} from './actions';
import {Settings} from './settings';
import {listenToCommonEvents} from './common-events';
import {init as initCodemirror} from './codemirror';
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
    const editor: MarkdownEditor = {
        config,
        markdown: new Markdown(),
        settings: new Settings(config.settingInputs),
    } as MarkdownEditor;

    editor.actions = new Actions(editor);
    editor.display = new Display(editor);

    const eventHandlers = getMarkdownDomEventHandlers(editor);
    const shortcuts = provideShortcutMap(editor);
    const onInputChange = () => editor.actions.updateAndRender();

    const initCodemirrorInput: () => Promise<MarkdownEditorInput> = async () => {
        const codeMirror = await initCodemirror(config.inputEl, shortcuts, eventHandlers, onInputChange);
        return new CodemirrorInput(codeMirror);
    };
    const initTextAreaInput: () => Promise<MarkdownEditorInput> = async () => {
        return new TextareaInput(config.inputEl, shortcuts, eventHandlers, onInputChange);
    };

    const isPlainEditor = Boolean(editor.settings.get('plainEditor'));
    editor.input = await (isPlainEditor ? initTextAreaInput() : initCodemirrorInput());
    editor.settings.onChange('plainEditor', async (value) => {
        const isPlain = Boolean(value);
        const newInput = await (isPlain ? initTextAreaInput() : initCodemirrorInput());
        editor.input.teardown();
        editor.input = newInput;
    });

    listenToCommonEvents(editor);

    return editor;
}


