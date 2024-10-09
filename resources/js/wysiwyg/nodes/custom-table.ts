import {SerializedTableNode, TableNode} from "@lexical/table";
import {DOMConversion, DOMConversionMap, DOMConversionOutput, LexicalNode, Spread} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";

import {el, extractStyleMapFromElement, StyleMap} from "../utils/dom";
import {getTableColumnWidths} from "../utils/tables";
import {
    CommonBlockAlignment, deserializeCommonBlockNode,
    SerializedCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "./_common";

export type SerializedCustomTableNode = Spread<Spread<{
    colWidths: string[];
    styles: Record<string, string>,
}, SerializedTableNode>, SerializedCommonBlockNode>

export class CustomTableNode extends TableNode {
    __id: string = '';
    __colWidths: string[] = [];
    __styles: StyleMap = new Map;
    __alignment: CommonBlockAlignment = '';
    __inset: number = 0;

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

    setAlignment(alignment: CommonBlockAlignment) {
        const self = this.getWritable();
        self.__alignment = alignment;
    }

    getAlignment(): CommonBlockAlignment {
        const self = this.getLatest();
        return self.__alignment;
    }

    setInset(size: number) {
        const self = this.getWritable();
        self.__inset = size;
    }

    getInset(): number {
        const self = this.getLatest();
        return self.__inset;
    }

    setColWidths(widths: string[]) {
        const self = this.getWritable();
        self.__colWidths = widths;
    }

    getColWidths(): string[] {
        const self = this.getLatest();
        return self.__colWidths;
    }

    getStyles(): StyleMap {
        const self = this.getLatest();
        return new Map(self.__styles);
    }

    setStyles(styles: StyleMap): void {
        const self = this.getWritable();
        self.__styles = new Map(styles);
    }

    static clone(node: CustomTableNode) {
        const newNode = new CustomTableNode(node.__key);
        newNode.__id = node.__id;
        newNode.__colWidths = node.__colWidths;
        newNode.__styles = new Map(node.__styles);
        newNode.__alignment = node.__alignment;
        newNode.__inset = node.__inset;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        updateElementWithCommonBlockProps(dom, this);

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

        for (const [name, value] of this.__styles.entries()) {
            dom.style.setProperty(name, value);
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
            styles: Object.fromEntries(this.__styles),
            alignment: this.__alignment,
            inset: this.__inset,
        };
    }

    static importJSON(serializedNode: SerializedCustomTableNode): CustomTableNode {
        const node = $createCustomTableNode();
        deserializeCommonBlockNode(serializedNode, node);
        node.setColWidths(serializedNode.colWidths);
        node.setStyles(new Map(Object.entries(serializedNode.styles)));
        return node;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            table(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        const node = $createCustomTableNode();
                        setCommonBlockPropsFromElement(element, node);

                        const colWidths = getTableColumnWidths(element as HTMLTableElement);
                        node.setColWidths(colWidths);
                        node.setStyles(extractStyleMapFromElement(element));

                        return {node};
                    },
                    priority: 1,
                };
            },
        };
    }
}

export function $createCustomTableNode(): CustomTableNode {
    return new CustomTableNode();
}

export function $isCustomTableNode(node: LexicalNode | null | undefined): node is CustomTableNode {
    return node instanceof CustomTableNode;
}
