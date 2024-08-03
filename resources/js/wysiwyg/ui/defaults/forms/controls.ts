import {EditorFormDefinition} from "../../framework/forms";
import {EditorUiContext} from "../../framework/core";
import {setEditorContentFromHtml} from "../../../utils/actions";

export const source: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        setEditorContentFromHtml(context.editor, formData.get('source')?.toString() || '');
        return true;
    },
    fields: [
        {
            label: 'Source',
            name: 'source',
            type: 'textarea',
        },
    ],
};