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
import {$getSelection, BaseSelection} from "lexical";
import {
    $deleteTableColumn__EXPERIMENTAL,
    $deleteTableRow__EXPERIMENTAL,
    $insertTableColumn__EXPERIMENTAL,
    $insertTableRow__EXPERIMENTAL, $isTableCellNode,
    $isTableNode, $isTableRowNode, $isTableSelection, $unmergeCell, TableCellNode,
} from "@lexical/table";
import {$getNodeFromSelection, $selectionContainsNodeType} from "../../../utils/selection";
import {$getParentOfType} from "../../../utils/nodes";
import {$showCellPropertiesForm, $showRowPropertiesForm, $showTablePropertiesForm} from "../forms/tables";
import {
    $clearTableFormatting,
    $clearTableSizes, $getTableFromSelection,
    $getTableRowsFromSelection,
    $mergeTableCellsInSelection
} from "../../../utils/tables";
import {
    $copySelectedColumnsToClipboard,
    $copySelectedRowsToClipboard,
    $cutSelectedColumnsToClipboard,
    $cutSelectedRowsToClipboard,
    $pasteClipboardRowsBefore,
    $pasteClipboardRowsAfter,
    isColumnClipboardEmpty,
    isRowClipboardEmpty,
    $pasteClipboardColumnsBefore, $pasteClipboardColumnsAfter
} from "../../../utils/table-copy-paste";

const neverActive = (): boolean => false;
const cellNotSelected = (selection: BaseSelection|null) => !$selectionContainsNodeType(selection, $isTableCellNode);

export const table: EditorBasicButtonDefinition = {
    label: 'Table',
    icon: tableIcon,
};

export const tableProperties: EditorButtonDefinition = {
    label: 'Table properties',
    icon: tableIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const table = $getTableFromSelection($getSelection());
            if ($isTableNode(table)) {
                $showTablePropertiesForm(table, context);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const clearTableFormatting: EditorButtonDefinition = {
    label: 'Clear table formatting',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
            if (!$isTableCellNode(cell)) {
                return;
            }

            const table = $getParentOfType(cell, $isTableNode);
            if ($isTableNode(table)) {
                $clearTableFormatting(table);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const resizeTableToContents: EditorButtonDefinition = {
    label: 'Resize to contents',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
            if (!$isTableCellNode(cell)) {
                return;
            }

            const table = $getParentOfType(cell, $isTableNode);
            if ($isTableNode(table)) {
                $clearTableSizes(table);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const deleteTable: EditorButtonDefinition = {
    label: 'Delete table',
    icon: deleteIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const table = $getNodeFromSelection($getSelection(), $isTableNode);
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
    label: 'Insert row before',
    icon: insertRowAboveIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertTableRow__EXPERIMENTAL(false);
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const insertRowBelow: EditorButtonDefinition = {
    label: 'Insert row after',
    icon: insertRowBelowIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertTableRow__EXPERIMENTAL(true);
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const deleteRow: EditorButtonDefinition = {
    label: 'Delete row',
    icon: deleteRowIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $deleteTableRow__EXPERIMENTAL();
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const rowProperties: EditorButtonDefinition = {
    label: 'Row properties',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const rows = $getTableRowsFromSelection($getSelection());
            if ($isTableRowNode(rows[0])) {
                $showRowPropertiesForm(rows[0], context);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const cutRow: EditorButtonDefinition = {
    label: 'Cut row',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            try {
                $cutSelectedRowsToClipboard();
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const copyRow: EditorButtonDefinition = {
    label: 'Copy row',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            try {
                $copySelectedRowsToClipboard();
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const pasteRowBefore: EditorButtonDefinition = {
    label: 'Paste row before',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            try {
                $pasteClipboardRowsBefore(context.editor);
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: (selection) => cellNotSelected(selection) || isRowClipboardEmpty(),
};

export const pasteRowAfter: EditorButtonDefinition = {
    label: 'Paste row after',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            try {
                $pasteClipboardRowsAfter(context.editor);
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: (selection) => cellNotSelected(selection) || isRowClipboardEmpty(),
};

export const cutColumn: EditorButtonDefinition = {
    label: 'Cut column',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            try {
                $cutSelectedColumnsToClipboard();
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const copyColumn: EditorButtonDefinition = {
    label: 'Copy column',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            try {
                $copySelectedColumnsToClipboard();
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const pasteColumnBefore: EditorButtonDefinition = {
    label: 'Paste column before',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            try {
                $pasteClipboardColumnsBefore(context.editor);
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: (selection) => cellNotSelected(selection) || isColumnClipboardEmpty(),
};

export const pasteColumnAfter: EditorButtonDefinition = {
    label: 'Paste column after',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            try {
                $pasteClipboardColumnsAfter(context.editor);
            } catch (e: any) {
                context.error(e);
            }
        });
    },
    isActive: neverActive,
    isDisabled: (selection) => cellNotSelected(selection) || isColumnClipboardEmpty(),
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
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
            if ($isTableCellNode(cell)) {
                $showCellPropertiesForm(cell, context);
            }
        });
    },
    isActive: neverActive,
    isDisabled: cellNotSelected,
};

export const mergeCells: EditorButtonDefinition = {
    label: 'Merge cells',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const selection = $getSelection();
            if ($isTableSelection(selection)) {
                $mergeTableCellsInSelection(selection);
            }
        });
    },
    isActive: neverActive,
    isDisabled(selection) {
        return !$isTableSelection(selection);
    }
};

export const splitCell: EditorButtonDefinition = {
    label: 'Split cell',
    format: 'long',
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $unmergeCell();
        });
    },
    isActive: neverActive,
    isDisabled(selection) {
        const cell = $getNodeFromSelection(selection, $isTableCellNode) as TableCellNode|null;
        if (cell) {
            const merged = cell.getRowSpan() > 1 || cell.getColSpan() > 1;
            return !merged;
        }

        return true;
    }
};