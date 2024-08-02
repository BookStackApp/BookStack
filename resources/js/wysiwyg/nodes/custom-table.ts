import {SerializedTableNode, TableNode, TableRowNode} from "@lexical/table";
import {DOMConversion, DOMConversionMap, DOMConversionOutput, LexicalEditor, LexicalNode, Spread} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {el} from "../helpers";

export type SerializedCustomTableNode = Spread<{
    id: string;
    colWidths: string[];
}, SerializedTableNode>

export class CustomTableNode extends TableNode {
    __id: string = '';
    __colWidths: string[] = [];

    static getType() {
        return 'custom-table';
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    setColWidths(widths: string[]) {
        const self = this.getWritable();
        self.__colWidths = widths;
    }

    getColWidths(): string[] {
        const self = this.getLatest();
        return self.__colWidths;
    }

    static clone(node: CustomTableNode) {
        const newNode = new CustomTableNode(node.__key);
        newNode.__id = node.__id;
        newNode.__colWidths = node.__colWidths;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        const id = this.getId();
        if (id) {
            dom.setAttribute('id', id);
        }

        const colWidths = this.getColWidths();
        if (colWidths.length > 0) {
            const colgroup = el('colgroup');
            for (const width of colWidths) {
                const col = el('col');
                if (width) {
                    col.style.width = width;
                }
                colgroup.append(col);
            }
            dom.append(colgroup);
        }

        return dom;
    }

    updateDOM(): boolean {
        return true;
    }

    exportJSON(): SerializedCustomTableNode {
        return {
            ...super.exportJSON(),
            type: 'custom-table',
            version: 1,
            id: this.__id,
            colWidths: this.__colWidths,
        };
    }

    static importJSON(serializedNode: SerializedCustomTableNode): CustomTableNode {
        const node = $createCustomTableNode();
        node.setId(serializedNode.id);
        node.setColWidths(serializedNode.colWidths);
        return node;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            table(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        const node = $createCustomTableNode();

                        if (element.id) {
                            node.setId(element.id);
                        }

                        const colWidths = getTableColumnWidths(element as HTMLTableElement);
                        node.setColWidths(colWidths);

                        return {node};
                    },
                    priority: 1,
                };
            },
        };
    }
}

function getTableColumnWidths(table: HTMLTableElement): string[] {
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

function getMaxColRowFromTable(table: HTMLTableElement): HTMLTableRowElement|null {
    const rows = table.querySelectorAll('tr');
    let maxColCount: number = 0;
    let maxColRow: HTMLTableRowElement|null = null;

    for (const row of rows) {
        if (row.childElementCount > maxColCount) {
            maxColRow = row;
            maxColCount = row.childElementCount;
        }
    }

    return maxColRow;
}

function extractWidthsFromRow(row: HTMLTableRowElement|HTMLTableColElement) {
    return [...row.children].map(child => extractWidthFromElement(child as HTMLElement))
}

function extractWidthFromElement(element: HTMLElement): string {
    let width = element.style.width || element.getAttribute('width');
    if (width && !Number.isNaN(Number(width))) {
        width = width + 'px';
    }

    return width || '';
}

export function $createCustomTableNode(): CustomTableNode {
    return new CustomTableNode();
}

export function $isCustomTableNode(node: LexicalNode | null | undefined): node is CustomTableNode {
    return node instanceof CustomTableNode;
}

export function $setTableColumnWidth(node: CustomTableNode, columnIndex: number, width: number): void {
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

    colWidths[columnIndex] = width + 'px';
    node.setColWidths(colWidths);
}

export function $getTableColumnWidth(editor: LexicalEditor, node: CustomTableNode, columnIndex: number): number {
    const colWidths = node.getColWidths();
    if (colWidths.length > columnIndex && colWidths[columnIndex].endsWith('px')) {
        return Number(colWidths[columnIndex].replace('px', ''));
    }

    // Otherwise, get from table element
    const table = editor.getElementByKey(node.__key) as HTMLTableElement|null;
    if (table) {
        const maxColRow = getMaxColRowFromTable(table);
        if (maxColRow && maxColRow.children.length > columnIndex) {
            const cell = maxColRow.children[columnIndex];
            return cell.clientWidth;
        }
    }

    return 0;
}