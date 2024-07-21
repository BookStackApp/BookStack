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
import {$getBlockElementNodesInSelection, $getNodeFromSelection, $getParentOfType} from "../../../helpers";
import {$getSelection} from "lexical";
import {$isCustomTableNode, CustomTableNode} from "../../../nodes/custom-table";
import {
    $deleteTableColumn, $deleteTableColumn__EXPERIMENTAL,
    $deleteTableRow__EXPERIMENTAL,
    $getTableRowIndexFromTableCellNode, $insertTableColumn, $insertTableColumn__EXPERIMENTAL,
    $insertTableRow, $insertTableRow__EXPERIMENTAL,
    $isTableCellNode,
    $isTableRowNode,
    TableCellNode
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