import {
    DOMConversionMap,
    DOMConversionOutput, ElementFormatType,
    LexicalNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {QuoteNode, SerializedQuoteNode} from "@lexical/rich-text";


export type SerializedCustomQuoteNode = Spread<{
    id: string;
}, SerializedQuoteNode>

export class CustomQuoteNode extends QuoteNode {
    __id: string = '';

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

    static clone(node: CustomQuoteNode) {
        const newNode = new CustomQuoteNode(node.__key);
        newNode.__id = node.__id;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        if (this.__id) {
            dom.setAttribute('id', this.__id);
        }

        return dom;
    }

    exportJSON(): SerializedCustomQuoteNode {
        return {
            ...super.exportJSON(),
            type: 'custom-quote',
            version: 1,
            id: this.__id,
        };
    }

    static importJSON(serializedNode: SerializedCustomQuoteNode): CustomQuoteNode {
        const node = $createCustomQuoteNode();
        node.setId(serializedNode.id);
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
    if (element.style !== null) {
        node.setFormat(element.style.textAlign as ElementFormatType);
    }
    if (element.id) {
        node.setId(element.id);
    }
    return {node};
}

export function $createCustomQuoteNode() {
    return new CustomQuoteNode();
}

export function $isCustomQuoteNode(node: LexicalNode | null | undefined): node is CustomQuoteNode {
    return node instanceof CustomQuoteNode;
}