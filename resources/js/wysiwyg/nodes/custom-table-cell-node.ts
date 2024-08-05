import {EditorConfig} from "lexical/LexicalEditor";
import {DOMExportOutput, LexicalEditor, LexicalNode, Spread} from "lexical";

import {SerializedTableCellNode, TableCellHeaderStates, TableCellNode} from "@lexical/table";
import {TableCellHeaderState} from "@lexical/table/LexicalTableCellNode";

export type SerializedCustomTableCellNode = Spread<{
    styles: Record<string, string>,
}, SerializedTableCellNode>

export class CustomTableCellNode extends TableCellNode {
    __styles: Map<string, string> = new Map;

    static getType(): string {
        return 'custom-table-cell';
    }

    static clone(node: CustomTableCellNode): CustomTableCellNode {
        const cellNode = new CustomTableCellNode(
            node.__headerState,
            node.__colSpan,
            node.__width,
            node.__key,
        );
        cellNode.__rowSpan = node.__rowSpan;
        cellNode.__styles = new Map(node.__styles);
        return cellNode;
    }

    getStyles(): Map<string, string> {
        const self = this.getLatest();
        return new Map(self.__styles);
    }

    setStyles(styles: Map<string, string>): void {
        const self = this.getWritable();
        self.__styles = new Map(styles);
    }

    updateTag(tag: string): void {
        const isHeader = tag.toLowerCase() === 'th';
        const state = isHeader ? TableCellHeaderStates.ROW : TableCellHeaderStates.NO_STATUS;
        const self = this.getWritable();
        self.__headerState = state;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const element = super.createDOM(config);

        for (const [name, value] of this.__styles.entries()) {
            element.style.setProperty(name, value);
        }

        return element;
    }

    // TODO - Import DOM

    updateDOM(prevNode: CustomTableCellNode): boolean {
        return super.updateDOM(prevNode)
            || this.__styles !== prevNode.__styles;
    }

    exportDOM(editor: LexicalEditor): DOMExportOutput {
        const element = this.createDOM(editor._config);
        return {
            element
        };
    }

    exportJSON(): SerializedCustomTableCellNode {
        return {
            ...super.exportJSON(),
            type: 'custom-table-cell',
            styles: Object.fromEntries(this.__styles),
        };
    }
}

export function $createCustomTableCellNode(
    headerState: TableCellHeaderState,
    colSpan = 1,
    width?: number,
): CustomTableCellNode {
    return new CustomTableCellNode(headerState, colSpan, width);
}

export function $isCustomTableCellNode(node: LexicalNode | null | undefined): node is CustomTableCellNode {
    return node instanceof CustomTableCellNode;
}