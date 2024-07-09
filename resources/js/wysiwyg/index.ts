import {createEditor, CreateEditorArgs, LexicalEditor} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {registerRichText} from '@lexical/rich-text';
import {mergeRegister} from '@lexical/utils';
import {getNodesForPageEditor} from './nodes';
import {buildEditorUI} from "./ui";
import {getEditorContentAsHtml, setEditorContentFromHtml} from "./actions";
import {registerTableResizer} from "./ui/framework/helpers/table-resizer";
import {el} from "./helpers";

export function createPageEditorInstance(container: HTMLElement, htmlContent: string): SimpleWysiwygEditorInterface {
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
        registerTableResizer(editor, editArea),
    );

    setEditorContentFromHtml(editor, htmlContent);

    const debugView = document.getElementById('lexical-debug');
    if (debugView) {
        debugView.hidden = true;
    }
    editor.registerUpdateListener(({editorState}) => {
        console.log('editorState', editorState.toJSON());
        if (debugView) {
            debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
        }
    });

    buildEditorUI(container, editArea, editor);

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