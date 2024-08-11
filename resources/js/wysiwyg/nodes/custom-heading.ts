import {
    DOMConversionMap,
    DOMConversionOutput, ElementFormatType,
    LexicalNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {HeadingNode, HeadingTagType, SerializedHeadingNode} from "@lexical/rich-text";


export type SerializedCustomHeadingNode = Spread<{
    id: string;
}, SerializedHeadingNode>

export class CustomHeadingNode extends HeadingNode {
    __id: string = '';

    static getType() {
        return 'custom-heading';
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    static clone(node: CustomHeadingNode) {
        const newNode = new CustomHeadingNode(node.__tag, node.__key);
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

    exportJSON(): SerializedCustomHeadingNode {
        return {
            ...super.exportJSON(),
            type: 'custom-heading',
            version: 1,
            id: this.__id,
        };
    }

    static importJSON(serializedNode: SerializedCustomHeadingNode): CustomHeadingNode {
        const node = $createCustomHeadingNode(serializedNode.tag);
        node.setId(serializedNode.id);
        return node;
    }

    static importDOM(): DOMConversionMap | null {
        return {
            h1: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h2: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h3: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h4: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h5: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h6: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
        };
    }
}

function $convertHeadingElement(element: HTMLElement): DOMConversionOutput {
    const nodeName = element.nodeName.toLowerCase();
    let node = null;
    if (
        nodeName === 'h1' ||
        nodeName === 'h2' ||
        nodeName === 'h3' ||
        nodeName === 'h4' ||
        nodeName === 'h5' ||
        nodeName === 'h6'
    ) {
        node = $createCustomHeadingNode(nodeName);
        if (element.style !== null) {
            node.setFormat(element.style.textAlign as ElementFormatType);
        }
        if (element.id) {
            node.setId(element.id);
        }
    }
    return {node};
}

export function $createCustomHeadingNode(tag: HeadingTagType) {
    return new CustomHeadingNode(tag);
}

export function $isCustomHeadingNode(node: LexicalNode | null | undefined): node is CustomHeadingNode {
    return node instanceof CustomHeadingNode;
}