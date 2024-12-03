import {EditorUiElement} from "../core";
import {$createTableNodeWithDimensions} from "@lexical/table";
import {$insertNewBlockNodeAtSelection} from "../../../utils/selection";
import {el} from "../../../utils/dom";


export class EditorTableCreator extends EditorUiElement {

    buildDOM(): HTMLElement {
        const size = 10;
        const rows: HTMLElement[] = [];
        const cells: HTMLElement[] = [];

        for (let row = 1; row < size + 1; row++) {
            const rowCells = [];
            for (let column = 1; column < size + 1; column++) {
                const cell = el('div', {
                    class: 'editor-table-creator-cell',
                    'data-rows': String(row),
                    'data-columns': String(column),
                });
                rowCells.push(cell);
                cells.push(cell);
            }
            rows.push(el('div', {
                class: 'editor-table-creator-row'
            }, rowCells));
        }

        const display = el('div', {class: 'editor-table-creator-display'}, ['0 x 0']);
        const grid = el('div', {class: 'editor-table-creator-grid'}, rows);
        grid.addEventListener('mousemove', event => {
            const cell = (event.target as HTMLElement).closest('.editor-table-creator-cell') as HTMLElement|null;
            if (cell) {
                const row = Number(cell.dataset.rows || 0);
                const column = Number(cell.dataset.columns || 0);
                this.updateGridSelection(row, column, cells, display)
            }
        });

        grid.addEventListener('click', event => {
            const cell = (event.target as HTMLElement).closest('.editor-table-creator-cell');
            if (cell) {
                this.onCellClick(cell as HTMLElement);
            }
        });

        grid.addEventListener('mouseleave', event => {
             this.updateGridSelection(0, 0, cells, display);
        });

        return el('div', {
            class: 'editor-table-creator',
        }, [
            grid,
            display,
        ]);
    }

    updateGridSelection(rows: number, columns: number, cells: HTMLElement[], display: HTMLElement) {
        for (const cell of cells) {
            const active = Number(cell.dataset.rows) <= rows && Number(cell.dataset.columns) <= columns;
            cell.classList.toggle('active', active);
        }

        display.textContent = `${rows} x ${columns}`;
    }

    onCellClick(cell: HTMLElement) {
        const rows = Number(cell.dataset.rows || 0);
        const columns = Number(cell.dataset.columns || 0);
        if (rows < 1 || columns < 1) {
            return;
        }

        const targetColWidth = Math.min(Math.round(840 / columns), 240);
        const colWidths = Array(columns).fill(targetColWidth + 'px');

        this.getContext().editor.update(() => {
            const table = $createTableNodeWithDimensions(rows, columns, false);
            table.setColWidths(colWidths);
            $insertNewBlockNodeAtSelection(table);
        });
    }
}