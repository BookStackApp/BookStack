import {$isTableCellNode, $isTableRowNode, TableCellNode, TableNode} from "@lexical/table";

export type CellRange = {
    fromX: number;
    fromY: number;
    toX: number;
    toY: number;
}

export class TableMap {

    rowCount: number = 0;
    columnCount: number = 0;

    // Represents an array (rows*columns in length) of cell nodes from top-left to
    // bottom right. Cells may repeat where merged and covering multiple spaces.
    cells: TableCellNode[] = [];

    constructor(table: TableNode) {
        this.buildCellMap(table);
    }

    protected buildCellMap(table: TableNode) {
        const rowsAndCells: TableCellNode[][] = [];
        const setCell = (x: number, y: number, cell: TableCellNode) => {
            if (typeof rowsAndCells[y] === 'undefined') {
                rowsAndCells[y] = [];
            }

            rowsAndCells[y][x] = cell;
        };
        const cellFilled = (x: number, y: number): boolean => !!(rowsAndCells[y] && rowsAndCells[y][x]);

        const rowNodes = table.getChildren().filter(r => $isTableRowNode(r));
        for (let rowIndex = 0; rowIndex < rowNodes.length; rowIndex++) {
            const rowNode = rowNodes[rowIndex];
            const cellNodes = rowNode.getChildren().filter(c => $isTableCellNode(c));
            let targetColIndex: number = 0;
            for (let cellIndex = 0; cellIndex < cellNodes.length; cellIndex++) {
                const cellNode = cellNodes[cellIndex];
                const colspan = cellNode.getColSpan() || 1;
                const rowSpan = cellNode.getRowSpan() || 1;
                for (let x = targetColIndex; x < targetColIndex + colspan; x++) {
                    for (let y = rowIndex; y < rowIndex + rowSpan; y++) {
                        while (cellFilled(x, y)) {
                            targetColIndex += 1;
                            x += 1;
                        }

                        setCell(x, y, cellNode);
                    }
                }
                targetColIndex += colspan;
            }
        }

        this.rowCount = rowsAndCells.length;
        this.columnCount = Math.max(...rowsAndCells.map(r => r.length));

        const cells = [];
        let lastCell: TableCellNode = rowsAndCells[0][0];
        for (let y = 0; y < this.rowCount; y++) {
            for (let x = 0; x < this.columnCount; x++) {
                if (!rowsAndCells[y] || !rowsAndCells[y][x]) {
                    cells.push(lastCell);
                } else {
                    cells.push(rowsAndCells[y][x]);
                    lastCell = rowsAndCells[y][x];
                }
            }
        }

        this.cells = cells;
    }

    public getCellAtPosition(x: number, y: number): TableCellNode {
        const position = (y * this.columnCount) + x;
        if (position >= this.cells.length) {
            throw new Error(`TableMap Error: Attempted to get cell ${position+1} of ${this.cells.length}`);
        }

        return this.cells[position];
    }

    public getCellsInRange(range: CellRange): TableCellNode[] {
        const minX = Math.max(Math.min(range.fromX, range.toX), 0);
        const maxX = Math.min(Math.max(range.fromX, range.toX), this.columnCount - 1);
        const minY = Math.max(Math.min(range.fromY, range.toY), 0);
        const maxY = Math.min(Math.max(range.fromY, range.toY), this.rowCount - 1);

        const cells = new Set<TableCellNode>();

        for (let y = minY; y <= maxY; y++) {
            for (let x = minX; x <= maxX; x++) {
                cells.add(this.getCellAtPosition(x, y));
            }
        }

        return [...cells.values()];
    }

    public getCellsInColumn(columnIndex: number): TableCellNode[] {
        return this.getCellsInRange({
            fromX: columnIndex,
            toX: columnIndex,
            fromY: 0,
            toY: this.rowCount - 1,
        });
    }

    public getRangeForCell(cell: TableCellNode): CellRange|null {
        let range: CellRange|null = null;
        const cellKey = cell.getKey();

        for (let y = 0; y < this.rowCount; y++) {
            for (let x = 0; x < this.columnCount; x++) {
                const index = (y * this.columnCount) + x;
                const lCell = this.cells[index];
                if (lCell.getKey() === cellKey) {
                    if (range === null) {
                        range = {fromX: x, toX: x, fromY: y, toY: y};
                    } else {
                        range.fromX = Math.min(range.fromX, x);
                        range.toX = Math.max(range.toX, x);
                        range.fromY = Math.min(range.fromY, y);
                        range.toY = Math.max(range.toY, y);
                    }
                }
            }
        }

        return range;
    }
}