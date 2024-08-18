import {
    DOMConversionMap,
    DOMConversionOutput,
    LexicalNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {QuoteNode, SerializedQuoteNode} from "@lexical/rich-text";
import {
    CommonBlockAlignment, commonPropertiesDifferent,
    SerializedCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "./_common";


export type SerializedCustomQuoteNode = Spread<SerializedCommonBlockNode, SerializedQuoteNode>

export class CustomQuoteNode extends QuoteNode {
    __id: string = '';
    __alignment: CommonBlockAlignment = '';

    static getType() {
        return 'custom-quote';
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

    static clone(node: CustomQuoteNode) {
        const newNode = new CustomQuoteNode(node.__key);
        newNode.__id = node.__id;
        newNode.__alignment = node.__alignment;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        updateElementWithCommonBlockProps(dom, this);
        return dom;
    }

    updateDOM(prevNode: CustomQuoteNode): boolean {
        return commonPropertiesDifferent(prevNode, this);
    }

    exportJSON(): SerializedCustomQuoteNode {
        return {
            ...super.exportJSON(),
            type: 'custom-quote',
            version: 1,
            id: this.__id,
            alignment: this.__alignment,
        };
    }

    static importJSON(serializedNode: SerializedCustomQuoteNode): CustomQuoteNode {
        const node = $createCustomQuoteNode();
        node.setId(serializedNode.id);
        node.setAlignment(serializedNode.alignment);
        return node;
    }

    static importDOM(): DOMConversionMap | null {
        return {
            blockquote: (node: Node) => ({
                conversion: $convertBlockquoteElement,
                priority: 0,
            }),
        };
    }
}

function $convertBlockquoteElement(element: HTMLElement): DOMConversionOutput {
    const node = $createCustomQuoteNode();
    setCommonBlockPropsFromElement(element, node);
    return {node};
}

export function $createCustomQuoteNode() {
    return new CustomQuoteNode();
}

export function $isCustomQuoteNode(node: LexicalNode | null | undefined): node is CustomQuoteNode {
    return node instanceof CustomQuoteNode;
}