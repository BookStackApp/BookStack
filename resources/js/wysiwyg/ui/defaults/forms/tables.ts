import {
    EditorFormDefinition,
    EditorFormFieldDefinition,
    EditorFormTabs,
    EditorSelectFormFieldDefinition
} from "../../framework/forms";
import {EditorUiContext} from "../../framework/core";

const borderStyleInput: EditorSelectFormFieldDefinition = {
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
};

const borderColorInput: EditorFormFieldDefinition = {
    label: 'Border color',
    name: 'border_color',
    type: 'text',
};

const backgroundColorInput: EditorFormFieldDefinition = {
    label: 'Background color',
    name: 'background_color',
    type: 'text',
};

const alignmentInput: EditorSelectFormFieldDefinition = {
    label: 'Alignment',
    name: 'align',
    type: 'select',
    valuesByLabel: {
        'None': '',
        'Left': 'left',
        'Center': 'center',
        'Right': 'right',
    }
};

export const cellProperties: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        // TODO
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
                        ...alignmentInput,
                        label: 'Horizontal align',
                        name: 'h_align',
                    },
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
                    borderStyleInput,
                    borderColorInput,
                    backgroundColorInput,
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

export const rowProperties: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        // TODO
        return true;
    },
    fields: [
        {
            build() {
                const generalFields: EditorFormFieldDefinition[] = [
                    {
                        label: 'Row type',
                        name: 'type',
                        type: 'select',
                        valuesByLabel: {
                            'Body': 'body',
                            'Header': 'header',
                            'Footer': 'footer',
                        }
                    } as EditorSelectFormFieldDefinition,
                    alignmentInput,
                    {
                        label: 'Height',
                        name: 'height',
                        type: 'text',
                    },
                ];

                const advancedFields: EditorFormFieldDefinition[] = [
                    borderStyleInput,
                    borderColorInput,
                    backgroundColorInput,
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

export const tableProperties: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        // TODO
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
                        label: 'Cell spacing',
                        name: 'cell_spacing',
                        type: 'text',
                    },
                    {
                        label: 'Cell padding',
                        name: 'cell_padding',
                        type: 'text',
                    },
                    {
                        label: 'Border width',
                        name: 'border_width',
                        type: 'text',
                    },
                    {
                        label: 'caption',
                        name: 'height',
                        type: 'text', // TODO -
                    },
                    alignmentInput,
                ];

                const advancedFields: EditorFormFieldDefinition[] = [
                    borderStyleInput,
                    borderColorInput,
                    backgroundColorInput,
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