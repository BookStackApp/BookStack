import {EditorFormDefinition, EditorSelectFormFieldDefinition} from "../framework/forms";
import {EditorUiContext} from "../framework/core";
import {$createLinkNode} from "@lexical/link";
import {$createTextNode, $getSelection} from "lexical";


export const link: EditorFormDefinition = {
    submitText: 'Apply',
    action(formData, context: EditorUiContext) {
        context.editor.update(() => {

            const selection = $getSelection();

            const linkNode = $createLinkNode(formData.get('url')?.toString() || '', {
                title: formData.get('title')?.toString() || '',
                target: formData.get('target')?.toString() || '',
            });
            linkNode.append($createTextNode(formData.get('text')?.toString() || ''));

            selection?.insertNodes([linkNode]);
        });
        return true;
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