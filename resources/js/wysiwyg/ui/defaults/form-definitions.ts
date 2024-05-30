import {EditorFormDefinition, EditorFormFieldDefinition, EditorSelectFormFieldDefinition} from "../framework/forms";
import {EditorUiContext} from "../framework/core";


export const link: EditorFormDefinition = {
    submitText: 'Apply',
    cancelText: 'Cancel',
    action(formData, context: EditorUiContext) {
        // Todo
        console.log('link-form-action', formData);
        return true;
    },
    cancel() {
        // Todo
        console.log('link-form-cancel');
    },
    fields: [
        {
            label: 'URL',
            name: 'url',
            type: 'text',
        },
        {
            label: 'Text to display',
            name: 'text',
            type: 'text',
        },
        {
            label: 'Title',
            name: 'title',
            type: 'text',
        },
        {
            label: 'Open link in...',
            name: 'target',
            type: 'select',
            valuesByLabel: {
                'Current window': '',
                'New window': '_blank',
            }
        } as EditorSelectFormFieldDefinition,
    ],
};