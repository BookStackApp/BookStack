import {
    EditorFormDefinition,
    EditorFormFieldDefinition,
    EditorFormTabs,
    EditorSelectFormFieldDefinition
} from "../../framework/forms";
import {EditorUiContext} from "../../framework/core";
import {$isCustomTableCellNode, CustomTableCellNode} from "../../../nodes/custom-table-cell-node";
import {EditorFormModal} from "../../framework/modals";
import {$getNodeFromSelection} from "../../../utils/selection";
import {$getSelection, ElementFormatType} from "lexical";
import {TableCellHeaderStates} from "@lexical/table";

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

export function showCellPropertiesForm(cell: CustomTableCellNode, context: EditorUiContext): EditorFormModal {
    const styles = cell.getStyles();
    const modalForm = context.manager.createModal('cell_properties');
    modalForm.show({
        width: '', // TODO
        height: styles.get('height') || '',
        type: cell.getTag(),
        h_align: '', // TODO
        v_align: styles.get('vertical-align') || '',
        border_width: styles.get('border-width') || '',
        border_style: styles.get('border-style') || '',
        border_color: styles.get('border-color') || '',
        background_color: styles.get('background-color') || '',
    });
    return modalForm;
}

export const cellProperties: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        // TODO - Set for cell selection range
        context.editor.update(() => {
            const cell = $getNodeFromSelection($getSelection(), $isCustomTableCellNode);
            if ($isCustomTableCellNode(cell)) {
                // TODO - Set width
                cell.setFormat((formData.get('h_align')?.toString() || '') as ElementFormatType);
                cell.updateTag(formData.get('type')?.toString() || '');

                const styles = cell.getStyles();
                styles.set('height', formData.get('height')?.toString() || '');
                styles.set('vertical-align', formData.get('v_align')?.toString() || '');
                styles.set('border-width', formData.get('border_width')?.toString() || '');
                styles.set('border-style', formData.get('border_style')?.toString() || '');
                styles.set('border-color', formData.get('border_color')?.toString() || '');
                styles.set('background-color', formData.get('background_color')?.toString() || '');

                cell.setStyles(styles);
            }
        });

        return true;
    },
    fields: [
        {
            build() {
                const generalFields: EditorFormFieldDefinition[] = [
                    {
                        label: 'Width', // Colgroup width
                        name: 'width',
                        type: 'text',
                    },
                    {
                        label: 'Height', // inline-style: height
                        name: 'height',
                        type: 'text',
                    },
                    {
                        label: 'Cell type', // element
                        name: 'type',
                        type: 'select',
                        valuesByLabel: {
                            'Cell': 'td',
                            'Header cell': 'th',
                        }
                    } as EditorSelectFormFieldDefinition,
                    {
                        ...alignmentInput, // class: 'align-right/left/center'
                        label: 'Horizontal align',
                        name: 'h_align',
                    },
                    {
                        label: 'Vertical align', // inline-style: vertical-align
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
                        label: 'Border width', // inline-style: border-width
                        name: 'border_width',
                        type: 'text',
                    },
                    borderStyleInput, // inline-style: border-style
                    borderColorInput, // inline-style: border-color
                    backgroundColorInput, // inline-style: background-color
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