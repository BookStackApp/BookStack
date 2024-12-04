import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    SerializedElementNode, Spread,
    EditorConfig,
} from 'lexical';

import {el} from "../../utils/dom";
import {extractDirectionFromElement} from "lexical/nodes/common";

export type SerializedDetailsNode = Spread<{
    id: string;
}, SerializedElementNode>

export class DetailsNode extends ElementNode {
    __id: string = '';

    static getType() {
        return 'details';
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    static clone(node: DetailsNode): DetailsNode {
        const newNode =  new DetailsNode(node.__key);
        newNode.__id = node.__id;
        newNode.__dir = node.__dir;
        return newNode;
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const el = document.createElement('details');
        if (this.__id) {
            el.setAttribute('id', this.__id);
        }

        if (this.__dir) {
            el.setAttribute('dir', this.__dir);
        }

        return el;
    }

    updateDOM(prevNode: DetailsNode, dom: HTMLElement) {
        return prevNode.__id !== this.__id
        || prevNode.__dir !== this.__dir;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            details(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        const node = new DetailsNode();
                        if (element.id) {
                            node.setId(element.id);
                        }

                        if (element.dir) {
                            node.setDirection(extractDirectionFromElement(element));
                        }

                        return {node};
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedDetailsNode {
        return {
            ...super.exportJSON(),
            type: 'details',
            version: 1,
            id: this.__id,
        };
    }

    static importJSON(serializedNode: SerializedDetailsNode): DetailsNode {
        const node = $createDetailsNode();
        node.setId(serializedNode.id);
        node.setDirection(serializedNode.direction);
        return node;
    }

}

export function $createDetailsNode() {
    return new DetailsNode();
}

export function $isDetailsNode(node: LexicalNode | null | undefined): node is DetailsNode {
    return node instanceof DetailsNode;
}

export class SummaryNode extends ElementNode {

    static getType() {
        return 'summary';
    }

    static clone(node: SummaryNode) {
        return new SummaryNode(node.__key);
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        return el('summary');
    }

    updateDOM(prevNode: DetailsNode, dom: HTMLElement) {
        return false;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            summary(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        return {
                            node: new SummaryNode(),
                        };
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedElementNode {
        return {
            ...super.exportJSON(),
            type: 'summary',
            version: 1,
        };
    }

    static importJSON(serializedNode: SerializedElementNode): SummaryNode {
        return $createSummaryNode();
    }

}

export function $createSummaryNode(): SummaryNode {
    return new SummaryNode();
}

export function $isSummaryNode(node: LexicalNode | null | undefined): node is SummaryNode {
    return node instanceof SummaryNode;
}
