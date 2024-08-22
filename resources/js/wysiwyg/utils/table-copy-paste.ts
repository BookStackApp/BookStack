import {NodeClipboard} from "./node-clipboard";
import {CustomTableRowNode} from "../nodes/custom-table-row";
import {$getTableFromSelection, $getTableRowsFromSelection} from "./tables";
import {$getSelection, LexicalEditor} from "lexical";
import {$createCustomTableCellNode, $isCustomTableCellNode} from "../nodes/custom-table-cell";
import {CustomTableNode} from "../nodes/custom-table";
import {TableMap} from "./table-map";

const rowClipboard: NodeClipboard<CustomTableRowNode> = new NodeClipboard<CustomTableRowNode>(CustomTableRowNode);

export function isRowClipboardEmpty(): boolean {
    return rowClipboard.size() === 0;
}

export function validateRowsToCopy(rows: CustomTableRowNode[]): void {
    let commonRowSize: number|null = null;

    for (const row of rows) {
        const cells = row.getChildren().filter(n => $isCustomTableCellNode(n));
        let rowSize = 0;
        for (const cell of cells) {
            rowSize += cell.getColSpan() || 1;
            if (cell.getRowSpan() > 1) {
                throw Error('Cannot copy rows with merged cells');
            }
        }

        if (commonRowSize === null) {
            commonRowSize = rowSize;
        } else if (commonRowSize !== rowSize) {
            throw Error('Cannot copy rows with inconsistent sizes');
        }
    }
}

export function validateRowsToPaste(rows: CustomTableRowNode[], targetTable: CustomTableNode): void {
    const tableColCount = (new TableMap(targetTable)).columnCount;
    for (const row of rows) {
        const cells = row.getChildren().filter(n => $isCustomTableCellNode(n));
        let rowSize = 0;
        for (const cell of cells) {
            rowSize += cell.getColSpan() || 1;
        }

        if (rowSize > tableColCount) {
            throw Error('Cannot paste rows that are wider than target table');
        }

        while (rowSize < tableColCount) {
            row.append($createCustomTableCellNode());
            rowSize++;
        }
    }
}

export function $cutSelectedRowsToClipboard(): void {
    const rows = $getTableRowsFromSelection($getSelection());
    validateRowsToCopy(rows);
    rowClipboard.set(...rows);
    for (const row of rows) {
        row.remove();
    }
}

export function $copySelectedRowsToClipboard(): void {
    const rows = $getTableRowsFromSelection($getSelection());
    validateRowsToCopy(rows);
    rowClipboard.set(...rows);
}

export function $pasteClipboardRowsBefore(editor: LexicalEditor): void {
    const selection = $getSelection();
    const rows = $getTableRowsFromSelection(selection);
    const table = $getTableFromSelection(selection);
    const lastRow = rows[rows.length - 1];
    if (lastRow && table) {
        const clipboardRows = rowClipboard.get(editor);
        validateRowsToPaste(clipboardRows, table);
        for (const row of clipboardRows) {
            lastRow.insertBefore(row);
        }
    }
}

export function $pasteRowsAfter(editor: LexicalEditor): void {
    const selection = $getSelection();
    const rows = $getTableRowsFromSelection(selection);
    const table = $getTableFromSelection(selection);
    const lastRow = rows[rows.length - 1];
    if (lastRow && table) {
        const clipboardRows = rowClipboard.get(editor).reverse();
        validateRowsToPaste(clipboardRows, table);
        for (const row of clipboardRows) {
            lastRow.insertAfter(row);
        }
    }
}