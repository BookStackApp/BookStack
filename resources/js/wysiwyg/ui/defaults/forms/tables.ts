import {
    EditorFormDefinition,
    EditorFormFieldDefinition,
    EditorFormTabs,
    EditorSelectFormFieldDefinition
} from "../../framework/forms";
import {EditorUiContext} from "../../framework/core";
import {setEditorContentFromHtml} from "../../../actions";

export const cellProperties: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        setEditorContentFromHtml(context.editor, formData.get('source')?.toString() || '');
        return true;
    },
    fields: [
        {
            build() {
                const generalFields: EditorFormFieldDefinition[] = [
                    {
                        label: 'Width',
                        name: 'width',
                        type: 'text',
                    },
                    {
                        label: 'Height',
                        name: 'height',
                        type: 'text',
                    },
                    {
                        label: 'Cell type',
                        name: 'type',
                        type: 'select',
                        valuesByLabel: {
                            'Cell': 'cell',
                            'Header cell': 'header',
                        }
                    } as EditorSelectFormFieldDefinition,
                    {
                        label: 'Horizontal align',
                        name: 'h_align',
                        type: 'select',
                        valuesByLabel: {
                            'None': '',
                            'Left': 'left',
                            'Center': 'center',
                            'Right': 'right',
                        }
                    } as EditorSelectFormFieldDefinition,
                    {
                        label: 'Vertical align',
                        name: 'v_align',
                        type: 'select',
                        valuesByLabel: {
                            'None': '',
                            'Top': 'top',
                            'Middle': 'middle',
                            'Bottom': 'bottom',
                        }
                    } as EditorSelectFormFieldDefinition,
                ];

                const advancedFields: EditorFormFieldDefinition[] = [
                    {
                        label: 'Border width',
                        name: 'border_width',
                        type: 'text',
                    },
                    {
                        label: 'Border style',
                        name: 'border_style',
                        type: 'select',
                        valuesByLabel: {
                            'Select...': '',
                            "Solid": 'solid',
                            "Dotted": 'dotted',
                            "Dashed": 'dashed',
                            "Double": 'double',
                            "Groove": 'groove',
                            "Ridge": 'ridge',
                            "Inset": 'inset',
                            "Outset": 'outset',
                            "None": 'none',
                            "Hidden": 'hidden',
                        }
                    } as EditorSelectFormFieldDefinition,
                    {
                        label: 'Border color',
                        name: 'border_color',
                        type: 'text',
                    },
                    {
                        label: 'Background color',
                        name: 'background_color',
                        type: 'text',
                    },
                ];

                return new EditorFormTabs([
                    {
                        label: 'General',
                        contents: generalFields,
                    },
                    {
                        label: 'Advanced',
                        contents: advancedFields,
                    }
                ])
            }
        },
    ],
};