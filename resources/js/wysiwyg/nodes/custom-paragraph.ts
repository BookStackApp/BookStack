import {
    DOMConversion,
    DOMConversionMap,
    DOMConversionOutput,
    LexicalNode,
    ParagraphNode, SerializedParagraphNode, Spread,
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {
    CommonBlockAlignment, commonPropertiesDifferent,
    SerializedCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "./_common";

export type SerializedCustomParagraphNode = Spread<SerializedCommonBlockNode, SerializedParagraphNode>

export class CustomParagraphNode extends ParagraphNode {
    __id: string = '';
    __alignment: CommonBlockAlignment = '';

    static getType() {
        return 'custom-paragraph';
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

    static clone(node: CustomParagraphNode): CustomParagraphNode {
        const newNode = new CustomParagraphNode(node.__key);
        newNode.__id = node.__id;
        newNode.__alignment = node.__alignment;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        updateElementWithCommonBlockProps(dom, this);
        return dom;
    }

    updateDOM(prevNode: CustomParagraphNode, dom: HTMLElement, config: EditorConfig): boolean {
        return super.updateDOM(prevNode, dom, config)
            || commonPropertiesDifferent(prevNode, this);
    }

    exportJSON(): SerializedCustomParagraphNode {
        return {
            ...super.exportJSON(),
            type: 'custom-paragraph',
            version: 1,
            id: this.__id,
            alignment: this.__alignment,
        };
    }

    static importJSON(serializedNode: SerializedCustomParagraphNode): CustomParagraphNode {
        const node = $createCustomParagraphNode();
        node.setId(serializedNode.id);
        node.setAlignment(serializedNode.alignment);
        return node;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            p(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        const node = $createCustomParagraphNode();
                        if (element.style.textIndent) {
                            const indent = parseInt(element.style.textIndent, 10) / 20;
                            if (indent > 0) {
                                node.setIndent(indent);
                            }
                        }

                        setCommonBlockPropsFromElement(element, node);

                        return {node};
                    },
                    priority: 1,
                };
            },
        };
    }
}

export function $createCustomParagraphNode(): CustomParagraphNode {
    return new CustomParagraphNode();
}

export function $isCustomParagraphNode(node: LexicalNode | null | undefined): node is CustomParagraphNode {
    return node instanceof CustomParagraphNode;
}