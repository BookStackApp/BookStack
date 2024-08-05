import {SerializedTableNode, TableNode} from "@lexical/table";
import {DOMConversion, DOMConversionMap, DOMConversionOutput, LexicalNode, Spread} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";

import {el} from "../utils/dom";
import {getTableColumnWidths} from "../utils/tables";

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

export function $createCustomTableNode(): CustomTableNode {
    return new CustomTableNode();
}

export function $isCustomTableNode(node: LexicalNode | null | undefined): node is CustomTableNode {
    return node instanceof CustomTableNode;
}
