import {BaseSelection, LexicalEditor} from "lexical";
import {$isTableRowNode, $isTableSelection, TableRowNode, TableSelection, TableSelectionShape} from "@lexical/table";
import {$isCustomTableNode, CustomTableNode} from "../nodes/custom-table";
import {$isCustomTableCellNode, CustomTableCellNode} from "../nodes/custom-table-cell-node";
import {$getParentOfType} from "./nodes";
import {$getNodeFromSelection} from "./selection";
import {formatSizeValue} from "./dom";
import {TableMap} from "./table-map";

function $getTableFromCell(cell: CustomTableCellNode): CustomTableNode|null {
    return $getParentOfType(cell, $isCustomTableNode) as CustomTableNode|null;
}

export function getTableColumnWidths(table: HTMLTableElement): string[] {
    const maxColRow = getMaxColRowFromTable(table);

    const colGroup = table.querySelector('colgroup');
    let widths: string[] = [];
    if (colGroup && (colGroup.childElementCount === maxColRow?.childElementCount || !maxColRow)) {
        widths = extractWidthsFromRow(colGroup);
    }
    if (widths.filter(Boolean).length === 0 && maxColRow) {
        widths = extractWidthsFromRow(maxColRow);
    }

    return widths;
}

function getMaxColRowFromTable(table: HTMLTableElement): HTMLTableRowElement | null {
    const rows = table.querySelectorAll('tr');
    let maxColCount: number = 0;
    let maxColRow: HTMLTableRowElement | null = null;

    for (const row of rows) {
        if (row.childElementCount > maxColCount) {
            maxColRow = row;
            maxColCount = row.childElementCount;
        }
    }

    return maxColRow;
}

function extractWidthsFromRow(row: HTMLTableRowElement | HTMLTableColElement) {
    return [...row.children].map(child => extractWidthFromElement(child as HTMLElement))
}

function extractWidthFromElement(element: HTMLElement): string {
    let width = element.style.width || element.getAttribute('width');
    if (width && !Number.isNaN(Number(width))) {
        width = width + 'px';
    }

    return width || '';
}

export function $setTableColumnWidth(node: CustomTableNode, columnIndex: number, width: number|string): void {
    const rows = node.getChildren() as TableRowNode[];
    let maxCols = 0;
    for (const row of rows) {
        const cellCount = row.getChildren().length;
        if (cellCount > maxCols) {
            maxCols = cellCount;
        }
    }

    let colWidths = node.getColWidths();
    if (colWidths.length === 0 || colWidths.length < maxCols) {
        colWidths = Array(maxCols).fill('');
    }

    if (columnIndex + 1 > colWidths.length) {
        console.error(`Attempted to set table column width for column [${columnIndex}] but only ${colWidths.length} columns found`);
    }

    colWidths[columnIndex] = formatSizeValue(width);
    node.setColWidths(colWidths);
}

export function $getTableColumnWidth(editor: LexicalEditor, node: CustomTableNode, columnIndex: number): number {
    const colWidths = node.getColWidths();
    if (colWidths.length > columnIndex && colWidths[columnIndex].endsWith('px')) {
        return Number(colWidths[columnIndex].replace('px', ''));
    }

    // Otherwise, get from table element
    const table = editor.getElementByKey(node.__key) as HTMLTableElement | null;
    if (table) {
        const maxColRow = getMaxColRowFromTable(table);
        if (maxColRow && maxColRow.children.length > columnIndex) {
            const cell = maxColRow.children[columnIndex];
            return cell.clientWidth;
        }
    }

    return 0;
}

function $getCellColumnIndex(node: CustomTableCellNode): number {
    const row = node.getParent();
    if (!$isTableRowNode(row)) {
        return -1;
    }

    let index = 0;
    const cells = row.getChildren<CustomTableCellNode>();
    for (const cell of cells) {
        let colSpan = cell.getColSpan() || 1;
        index += colSpan;
        if (cell.getKey() === node.getKey()) {
            break;
        }
    }

    return index - 1;
}

export function $setTableCellColumnWidth(cell: CustomTableCellNode, width: string): void {
    const table = $getTableFromCell(cell)
    const index = $getCellColumnIndex(cell);

    if (table && index >= 0) {
        $setTableColumnWidth(table, index, width);
    }
}

export function $getTableCellsFromSelection(selection: BaseSelection|null): CustomTableCellNode[]  {
    if ($isTableSelection(selection)) {
        const nodes = selection.getNodes();
        return nodes.filter(n => $isCustomTableCellNode(n));
    }

    const cell = $getNodeFromSelection(selection, $isCustomTableCellNode) as CustomTableCellNode;
    return cell ? [cell] : [];
}

export function $mergeTableCellsInSelection(selection: TableSelection): void {
    const selectionShape = selection.getShape();
    const cells = $getTableCellsFromSelection(selection);
    if (cells.length === 0) {
        return;
    }

    const table = $getTableFromCell(cells[0]);
    if (!table) {
        return;
    }

    const tableMap = new TableMap(table);
    const headCell = tableMap.getCellAtPosition(selectionShape.toX, selectionShape.toY);
    if (!headCell) {
        return;
    }

    // We have to adjust the shape since it won't take into account spans for the head corner position.
    const fixedToX = selectionShape.toX + ((headCell.getColSpan() || 1) - 1);
    const fixedToY = selectionShape.toY + ((headCell.getRowSpan() || 1) - 1);

    const mergeCells = tableMap.getCellsInRange(
        selectionShape.fromX,
        selectionShape.fromY,
        fixedToX,
        fixedToY,
    );

    if (mergeCells.length === 0) {
        return;
    }

    const firstCell = mergeCells[0];
    const newWidth = Math.abs(selectionShape.fromX - fixedToX) + 1;
    const newHeight = Math.abs(selectionShape.fromY - fixedToY) + 1;

    for (let i = 1; i < mergeCells.length; i++) {
        const mergeCell = mergeCells[i];
        firstCell.append(...mergeCell.getChildren());
        mergeCell.remove();
    }

    firstCell.setColSpan(newWidth);
    firstCell.setRowSpan(newHeight);
}











