import {EditorFormDefinition} from "../../framework/forms";
import {EditorUiContext, EditorUiElement} from "../../framework/core";
import {focusEditor, setEditorContentFromHtml} from "../../../utils/actions";
import {ExternalContent} from "../../framework/blocks/external-content";

export const source: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        setEditorContentFromHtml(context.editor, formData.get('source')?.toString() || '');
        context.editor.commitUpdates();
        window.requestAnimationFrame(() => {
            focusEditor(context.editor);
        });
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

export const about: EditorFormDefinition = {
    submitText: 'Close',
    async action() {
        return true;
    },
    fields: [
        {
            build(): EditorUiElement {
                return new ExternalContent('/help/wysiwyg');
            }
        }
    ],
};