import {createEditor, CreateEditorArgs, LexicalEditor} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {registerRichText} from '@lexical/rich-text';
import {mergeRegister} from '@lexical/utils';
import {getNodesForPageEditor, registerCommonNodeMutationListeners} from './nodes';
import {buildEditorUI} from "./ui";
import {getEditorContentAsHtml, setEditorContentFromHtml} from "./utils/actions";
import {registerTableResizer} from "./ui/framework/helpers/table-resizer";
import {EditorUiContext} from "./ui/framework/core";
import {listen as listenToCommonEvents} from "./services/common-events";
import {handleDropEvents} from "./services/drop-handling";
import {registerTaskListHandler} from "./ui/framework/helpers/task-list-handler";
import {registerTableSelectionHandler} from "./ui/framework/helpers/table-selection-handler";
import {el} from "./utils/dom";

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

    const editor = createEditor(config);
    editor.setRootElement(editArea);

    mergeRegister(
        registerRichText(editor),
        registerHistory(editor, createEmptyHistoryState(), 300),
        registerTableResizer(editor, editWrap),
        registerTableSelectionHandler(editor),
        registerTaskListHandler(editor, editArea),
    );

    listenToCommonEvents(editor);
    handleDropEvents(editor);

    setEditorContentFromHtml(editor, htmlContent);

    const debugView = document.getElementById('lexical-debug');
    if (debugView) {
        debugView.hidden = true;
    }

    let changeFromLoading = true;
    editor.registerUpdateListener(({editorState, dirtyElements, dirtyLeaves}) => {

        // Emit change event to component system (for draft detection) on actual user content change
        if (dirtyElements.size > 0 || dirtyLeaves.size > 0) {
            if (changeFromLoading) {
                changeFromLoading = false;
            } else {
                window.$events.emit('editor-html-change', '');
            }
        }

        // Debug logic
        // console.log('editorState', editorState.toJSON());
        if (debugView) {
            debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
        }
    });

    // @ts-ignore
    window.debugEditorState = () => {
        console.log(editor.getEditorState().toJSON());
    };

    const context: EditorUiContext = buildEditorUI(container, editArea, editWrap, editor, options);
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