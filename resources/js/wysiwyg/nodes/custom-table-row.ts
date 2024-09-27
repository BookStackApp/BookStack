import {
    DOMConversionMap,
    DOMConversionOutput,
    EditorConfig,
    LexicalNode,
    Spread
} from "lexical";

import {
    SerializedTableRowNode,
    TableRowNode
} from "@lexical/table";
import {NodeKey} from "lexical/LexicalNode";
import {extractStyleMapFromElement, StyleMap} from "../utils/dom";

export type SerializedCustomTableRowNode = Spread<{
    styles: Record<string, string>,
}, SerializedTableRowNode>

export class CustomTableRowNode extends TableRowNode {
    __styles: StyleMap = new Map();

    constructor(key?: NodeKey) {
        super(0, key);
    }

    static getType(): string {
        return 'custom-table-row';
    }

    static clone(node: CustomTableRowNode): CustomTableRowNode {
        const cellNode = new CustomTableRowNode(node.__key);

        cellNode.__styles = new Map(node.__styles);
        return cellNode;
    }

    getStyles(): StyleMap {
        const self = this.getLatest();
        return new Map(self.__styles);
    }

    setStyles(styles: StyleMap): void {
        const self = this.getWritable();
        self.__styles = new Map(styles);
    }

    createDOM(config: EditorConfig): HTMLElement {
        const element = super.createDOM(config);

        for (const [name, value] of this.__styles.entries()) {
            element.style.setProperty(name, value);
        }

        return element;
    }

    updateDOM(prevNode: CustomTableRowNode): boolean {
        return super.updateDOM(prevNode)
            || this.__styles !== prevNode.__styles;
    }

    static importDOM(): DOMConversionMap | null {
        return {
            tr: (node: Node) => ({
                conversion: $convertTableRowElement,
                priority: 0,
            }),
        };
    }

    static importJSON(serializedNode: SerializedCustomTableRowNode): CustomTableRowNode {
        const node = $createCustomTableRowNode();

        node.setStyles(new Map(Object.entries(serializedNode.styles)));

        return node;
    }

    exportJSON(): SerializedCustomTableRowNode {
        return {
            ...super.exportJSON(),
            height: 0,
            type: 'custom-table-row',
            styles: Object.fromEntries(this.__styles),
        };
    }
}

export function $convertTableRowElement(domNode: Node): DOMConversionOutput {
    const rowNode = $createCustomTableRowNode();

    if (domNode instanceof HTMLElement) {
        rowNode.setStyles(extractStyleMapFromElement(domNode));
    }

    return {node: rowNode};
}

export function $createCustomTableRowNode(): CustomTableRowNode {
    return new CustomTableRowNode();
}

export function $isCustomTableRowNode(node: LexicalNode | null | undefined): node is CustomTableRowNode {
    return node instanceof CustomTableRowNode;
}