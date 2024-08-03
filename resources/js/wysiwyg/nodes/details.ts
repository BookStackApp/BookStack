import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    SerializedElementNode,
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";

import {el} from "../utils/dom";

export class DetailsNode extends ElementNode {

    static getType() {
        return 'details';
    }

    static clone(node: DetailsNode) {
        return new DetailsNode(node.__key);
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        return el('details');
    }

    updateDOM(prevNode: DetailsNode, dom: HTMLElement) {
        return false;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            details(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        return {
                            node: new DetailsNode(),
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
            type: 'details',
            version: 1,
        };
    }

    static importJSON(serializedNode: SerializedElementNode): DetailsNode {
        return $createDetailsNode();
    }

}

export function $createDetailsNode() {
    return new DetailsNode();
}

export function $isDetailsNode(node: LexicalNode | null | undefined) {
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

    static importJSON(serializedNode: SerializedElementNode): DetailsNode {
        return $createSummaryNode();
    }

}

export function $createSummaryNode() {
    return new SummaryNode();
}

export function $isSummaryNode(node: LexicalNode | null | undefined) {
    return node instanceof SummaryNode;
}
