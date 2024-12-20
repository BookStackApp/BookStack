import {$getSelection, createEditor, CreateEditorArgs, LexicalEditor} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {registerRichText} from '@lexical/rich-text';
import {mergeRegister} from '@lexical/utils';
import {getNodesForPageEditor, registerCommonNodeMutationListeners} from './nodes';
import {buildEditorUI} from "./ui";
import {getEditorContentAsHtml, setEditorContentFromHtml} from "./utils/actions";
import {registerTableResizer} from "./ui/framework/helpers/table-resizer";
import {EditorUiContext} from "./ui/framework/core";
import {listen as listenToCommonEvents} from "./services/common-events";
import {registerDropPasteHandling} from "./services/drop-paste-handling";
import {registerTaskListHandler} from "./ui/framework/helpers/task-list-handler";
import {registerTableSelectionHandler} from "./ui/framework/helpers/table-selection-handler";
import {el} from "./utils/dom";
import {registerShortcuts} from "./services/shortcuts";
import {registerNodeResizer} from "./ui/framework/helpers/node-resizer";
import {registerKeyboardHandling} from "./services/keyboard-handling";
import {registerAutoLinks} from "./services/auto-links";

export function createPageEditorInstance(container: HTMLElement, htmlContent: string, options: Record<string, any> = {}): SimpleWysiwygEditorInterface {
    const config: CreateEditorArgs = {
        namespace: 'BookStackPageEditor',
        nodes: getNodesForPageEditor(),
        onError: console.error,
        theme: {
            text: {
                bold: 'editor-theme-bold',
                code: 'editor-theme-code',
                italic: 'editor-theme-italic',
                strikethrough: 'editor-theme-strikethrough',
                subscript: 'editor-theme-subscript',
                superscript: 'editor-theme-superscript',
                underline: 'editor-theme-underline',
                underlineStrikethrough: 'editor-theme-underline-strikethrough',
            }
        }
    };

    const editArea = el('div', {
        contenteditable: 'true',
        class: 'editor-content-area page-content',
    });
    const editWrap = el('div', {
        class: 'editor-content-wrap',
    }, [editArea]);

    container.append(editWrap);
    container.classList.add('editor-container');
    container.setAttribute('dir', options.textDirection);
    if (options.darkMode) {
        container.classList.add('editor-dark');
    }

    const editor = createEditor(config);
    editor.setRootElement(editArea);
    const context: EditorUiContext = buildEditorUI(container, editArea, editWrap, editor, options);

    mergeRegister(
        registerRichText(editor),
        registerHistory(editor, createEmptyHistoryState(), 300),
        registerShortcuts(context),
        registerKeyboardHandling(context),
        registerTableResizer(editor, editWrap),
        registerTableSelectionHandler(editor),
        registerTaskListHandler(editor, editArea),
        registerDropPasteHandling(context),
        registerNodeResizer(context),
        registerAutoLinks(editor),
    );

    listenToCommonEvents(editor);

    setEditorContentFromHtml(editor, htmlContent);

    const debugView = document.getElementById('lexical-debug');
    if (debugView) {
        debugView.hidden = true;
        editor.registerUpdateListener(({dirtyElements, dirtyLeaves, editorState, prevEditorState}) => {
            // Debug logic
            // console.log('editorState', editorState.toJSON());
            debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
        });
    }

    // @ts-ignore
    window.debugEditorState = () => {
        console.log(editor.getEditorState().toJSON());
    };

    registerCommonNodeMutationListeners(context);

    return new SimpleWysiwygEditorInterface(editor);
}

export class SimpleWysiwygEditorInterface {
    protected editor: LexicalEditor;

    constructor(editor: LexicalEditor) {
        this.editor = editor;
    }

    async getContentAsHtml(): Promise<string> {
        return await getEditorContentAsHtml(this.editor);
    }
}