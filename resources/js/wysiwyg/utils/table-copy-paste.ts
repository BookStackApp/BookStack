import {NodeClipboard} from "./node-clipboard";
import {$getTableCellsFromSelection, $getTableFromSelection, $getTableRowsFromSelection} from "./tables";
import {$getSelection, BaseSelection, LexicalEditor} from "lexical";
import {TableMap} from "./table-map";
import {
    $createTableCellNode,
    $isTableCellNode,
    $isTableSelection,
    TableCellNode,
    TableNode,
    TableRowNode
} from "@lexical/table";
import {$getNodeFromSelection} from "./selection";

const rowClipboard: NodeClipboard<TableRowNode> = new NodeClipboard<TableRowNode>();

export function isRowClipboardEmpty(): boolean {
    return rowClipboard.size() === 0;
}

export function validateRowsToCopy(rows: TableRowNode[]): void {
    let commonRowSize: number|null = null;

    for (const row of rows) {
        const cells = row.getChildren().filter(n => $isTableCellNode(n));
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

export function validateRowsToPaste(rows: TableRowNode[], targetTable: TableNode): void {
    const tableColCount = (new TableMap(targetTable)).columnCount;
    for (const row of rows) {
        const cells = row.getChildren().filter(n => $isTableCellNode(n));
        let rowSize = 0;
        for (const cell of cells) {
            rowSize += cell.getColSpan() || 1;
        }

        if (rowSize > tableColCount) {
            throw Error('Cannot paste rows that are wider than target table');
        }

        while (rowSize < tableColCount) {
            row.append($createTableCellNode());
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

export function $pasteClipboardRowsAfter(editor: LexicalEditor): void {
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

const columnClipboard: NodeClipboard<TableCellNode>[] = [];

function setColumnClipboard(columns: TableCellNode[][]): void {
    const newClipboards = columns.map(cells => {
        const clipboard = new NodeClipboard<TableCellNode>();
        clipboard.set(...cells);
        return clipboard;
    });

    columnClipboard.splice(0, columnClipboard.length, ...newClipboards);
}

type TableRange = {from: number, to: number};

export function isColumnClipboardEmpty(): boolean {
    return columnClipboard.length === 0;
}

function $getSelectionColumnRange(selection: BaseSelection|null): TableRange|null {
    if ($isTableSelection(selection)) {
        const shape = selection.getShape()
        return {from: shape.fromX, to: shape.toX};
    }

    const cell = $getNodeFromSelection(selection, $isTableCellNode);
    const table = $getTableFromSelection(selection);
    if (!$isTableCellNode(cell) || !table) {
        return null;
    }

    const map = new TableMap(table);
    const range = map.getRangeForCell(cell);
    if (!range) {
        return null;
    }

    return {from: range.fromX, to: range.toX};
}

function $getTableColumnCellsFromSelection(range: TableRange, table: TableNode): TableCellNode[][] {
    const map = new TableMap(table);
    const columns = [];
    for (let x = range.from; x <= range.to; x++) {
        const cells = map.getCellsInColumn(x);
        columns.push(cells);
    }

    return columns;
}

function validateColumnsToCopy(columns: TableCellNode[][]): void {
    let commonColSize: number|null = null;

    for (const cells of columns) {
        let colSize = 0;
        for (const cell of cells) {
            colSize += cell.getRowSpan() || 1;
            if (cell.getColSpan() > 1) {
                throw Error('Cannot copy columns with merged cells');
            }
        }

        if (commonColSize === null) {
            commonColSize = colSize;
        } else if (commonColSize !== colSize) {
            throw Error('Cannot copy columns with inconsistent sizes');
        }
    }
}

export function $cutSelectedColumnsToClipboard(): void {
    const selection = $getSelection();
    const range = $getSelectionColumnRange(selection);
    const table = $getTableFromSelection(selection);
    if (!range || !table) {
        return;
    }

    const colWidths = table.getColWidths();
    const columns = $getTableColumnCellsFromSelection(range, table);
    validateColumnsToCopy(columns);
    setColumnClipboard(columns);
    for (const cells of columns) {
        for (const cell of cells) {
            cell.remove();
        }
    }

    const newWidths = [...colWidths].splice(range.from, (range.to - range.from) + 1);
    table.setColWidths(newWidths);
}

export function $copySelectedColumnsToClipboard(): void {
    const selection = $getSelection();
    const range = $getSelectionColumnRange(selection);
    const table = $getTableFromSelection(selection);
    if (!range || !table) {
        return;
    }

    const columns = $getTableColumnCellsFromSelection(range, table);
    validateColumnsToCopy(columns);
    setColumnClipboard(columns);
}

function validateColumnsToPaste(columns: TableCellNode[][], targetTable: TableNode) {
    const tableRowCount = (new TableMap(targetTable)).rowCount;
    for (const cells of columns) {
        let colSize = 0;
        for (const cell of cells) {
            colSize += cell.getRowSpan() || 1;
        }

        if (colSize > tableRowCount) {
            throw Error('Cannot paste columns that are taller than target table');
        }

        while (colSize < tableRowCount) {
            cells.push($createTableCellNode());
            colSize++;
        }
    }
}

function $pasteClipboardColumns(editor: LexicalEditor, isBefore: boolean): void {
    const selection = $getSelection();
    const table = $getTableFromSelection(selection);
    const cells = $getTableCellsFromSelection(selection);
    const referenceCell = cells[isBefore ? 0 : cells.length - 1];
    if (!table || !referenceCell) {
        return;
    }

    const clipboardCols = columnClipboard.map(cb => cb.get(editor));
    if (!isBefore) {
        clipboardCols.reverse();
    }

    validateColumnsToPaste(clipboardCols, table);
    const map = new TableMap(table);
    const cellRange = map.getRangeForCell(referenceCell);
    if (!cellRange) {
        return;
    }

    const colIndex = isBefore ? cellRange.fromX : cellRange.toX;
    const colWidths = table.getColWidths();

    for (let y = 0; y < map.rowCount; y++) {
        const relCell = map.getCellAtPosition(colIndex, y);
        for (const cells of clipboardCols) {
            const newCell = cells[y];
            if (isBefore) {
                relCell.insertBefore(newCell);
            } else {
                relCell.insertAfter(newCell);
            }
        }
    }

    const refWidth = colWidths[colIndex];
    const addedWidths = clipboardCols.map(_ => refWidth);
    colWidths.splice(isBefore ? colIndex : colIndex + 1, 0, ...addedWidths);
}

export function $pasteClipboardColumnsBefore(editor: LexicalEditor): void {
    $pasteClipboardColumns(editor, true);
}

export function $pasteClipboardColumnsAfter(editor: LexicalEditor): void {
    $pasteClipboardColumns(editor, false);
}