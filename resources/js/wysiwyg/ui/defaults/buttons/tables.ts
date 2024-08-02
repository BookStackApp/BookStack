import {EditorBasicButtonDefinition, EditorButtonDefinition} from "../../framework/buttons";
import tableIcon from "@icons/editor/table.svg";
import deleteIcon from "@icons/editor/table-delete.svg";
import deleteColumnIcon from "@icons/editor/table-delete-column.svg";
import deleteRowIcon from "@icons/editor/table-delete-row.svg";
import insertColumnAfterIcon from "@icons/editor/table-insert-column-after.svg";
import insertColumnBeforeIcon from "@icons/editor/table-insert-column-before.svg";
import insertRowAboveIcon from "@icons/editor/table-insert-row-above.svg";
import insertRowBelowIcon from "@icons/editor/table-insert-row-below.svg";
import {EditorUiContext} from "../../framework/core";
import {
    $getNodeFromSelection,
    $selectionContainsNodeType
} from "../../../helpers";
import {$getSelection} from "lexical";
import {$isCustomTableNode} from "../../../nodes/custom-table";
import {
    $deleteTableColumn__EXPERIMENTAL,
    $deleteTableRow__EXPERIMENTAL,
    $insertTableColumn__EXPERIMENTAL,
    $insertTableRow__EXPERIMENTAL, $isTableCellNode,
    $isTableNode, $isTableSelection, $unmergeCell, TableCellNode,
} from "@lexical/table";


export const table: EditorBasicButtonDefinition = {
    label: 'Table',
    icon: tableIcon,
};

export const deleteTable: EditorButtonDefinition = {
    label: 'Delete table',
    icon: deleteIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const table = $getNodeFromSelection($getSelection(), $isCustomTableNode);
            if (table) {
                table.remove();
            }
        });
    },
    isActive() {
        return false;
    }
};

export const deleteTableMenuAction: EditorButtonDefinition = {
    ...deleteTable,
    format: 'long',
    isDisabled(selection) {
        return !$selectionContainsNodeType(selection, $isTableNode);
    },
};

export const insertRowAbove: EditorButtonDefinition = {
    label: 'Insert row above',
    icon: insertRowAboveIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertTableRow__EXPERIMENTAL(false);
        });
    },
    isActive() {
        return false;
    }
};

export const insertRowBelow: EditorButtonDefinition = {
    label: 'Insert row below',
    icon: insertRowBelowIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertTableRow__EXPERIMENTAL(true);
        });
    },
    isActive() {
        return false;
    }
};

export const deleteRow: EditorButtonDefinition = {
    label: 'Delete row',
    icon: deleteRowIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $deleteTableRow__EXPERIMENTAL();
        });
    },
    isActive() {
        return false;
    }
};

export const insertColumnBefore: EditorButtonDefinition = {
    label: 'Insert column before',
    icon: insertColumnBeforeIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertTableColumn__EXPERIMENTAL(false);
        });
    },
    isActive() {
        return false;
    }
};

export const insertColumnAfter: EditorButtonDefinition = {
    label: 'Insert column after',
    icon: insertColumnAfterIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertTableColumn__EXPERIMENTAL(true);
        });
    },
    isActive() {
        return false;
    }
};

export const deleteColumn: EditorButtonDefinition = {
    label: 'Delete column',
    icon: deleteColumnIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $deleteTableColumn__EXPERIMENTAL();
        });
    },
    isActive() {
        return false;
    }
};

export const cellProperties: EditorButtonDefinition = {
    label: 'Cell properties',
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
            if ($isTableCellNode(cell)) {

                const modalForm = context.manager.createModal('cell_properties');
                modalForm.show({});
            }
        });
    },
    isActive() {
        return false;
    },
    isDisabled(selection) {
        return !$selectionContainsNodeType(selection, $isTableCellNode);
    }
};

export const mergeCells: EditorButtonDefinition = {
    label: 'Merge cells',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            // Todo - Needs to be done manually
            // Playground reference:
            // https://github.com/facebook/lexical/blob/f373759a7849f473d34960a6bf4e34b2a011e762/packages/lexical-playground/src/plugins/TableActionMenuPlugin/index.tsx#L299
        });
    },
    isActive() {
        return false;
    },
    isDisabled(selection) {
        return !$isTableSelection(selection);
    }
};

export const splitCell: EditorButtonDefinition = {
    label: 'Split cell',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $unmergeCell();
        });
    },
    isActive() {
        return false;
    },
    isDisabled(selection) {
        const cell = $getNodeFromSelection(selection, $isTableCellNode) as TableCellNode|null;
        if (cell) {
            const merged = cell.getRowSpan() > 1 || cell.getColSpan() > 1;
            return !merged;
        }

        return true;
    }
};