import {
    DOMConversionMap,
    DOMConversionOutput,
    LexicalNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {HeadingNode, HeadingTagType, SerializedHeadingNode} from "@lexical/rich-text";
import {
    CommonBlockAlignment, commonPropertiesDifferent, deserializeCommonBlockNode,
    SerializedCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "./_common";


export type SerializedCustomHeadingNode = Spread<SerializedCommonBlockNode, SerializedHeadingNode>

export class CustomHeadingNode extends HeadingNode {
    __id: string = '';
    __alignment: CommonBlockAlignment = '';
    __inset: number = 0;

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

    static clone(node: CustomHeadingNode) {
        const newNode = new CustomHeadingNode(node.__tag, node.__key);
        newNode.__alignment = node.__alignment;
        newNode.__inset = node.__inset;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        updateElementWithCommonBlockProps(dom, this);
        return dom;
    }

    updateDOM(prevNode: CustomHeadingNode, dom: HTMLElement): boolean {
        return super.updateDOM(prevNode, dom)
            || commonPropertiesDifferent(prevNode, this);
    }

    exportJSON(): SerializedCustomHeadingNode {
        return {
            ...super.exportJSON(),
            type: 'custom-heading',
            version: 1,
            id: this.__id,
            alignment: this.__alignment,
            inset: this.__inset,
        };
    }

    static importJSON(serializedNode: SerializedCustomHeadingNode): CustomHeadingNode {
        const node = $createCustomHeadingNode(serializedNode.tag);
        deserializeCommonBlockNode(serializedNode, node);
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
        setCommonBlockPropsFromElement(element, node);
    }
    return {node};
}

export function $createCustomHeadingNode(tag: HeadingTagType) {
    return new CustomHeadingNode(tag);
}

export function $isCustomHeadingNode(node: LexicalNode | null | undefined): node is CustomHeadingNode {
    return node instanceof CustomHeadingNode;
}