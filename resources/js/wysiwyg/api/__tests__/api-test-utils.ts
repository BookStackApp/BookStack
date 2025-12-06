import {createTestContext} from "lexical/__tests__/utils";
import {EditorApi} from "../api";
import {EditorUiContext} from "../../ui/framework/core";
import {LexicalEditor} from "lexical";


/**
 * Create an instance of the EditorApi and EditorUiContext.
 */
export function createEditorApiInstance(): { api: EditorApi; context: EditorUiContext, editor: LexicalEditor} {
    const context = createTestContext();
    const api = new EditorApi(context);
    return {api, context, editor: context.editor};
}