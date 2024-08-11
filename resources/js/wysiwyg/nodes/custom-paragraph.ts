import {
    DOMConversion,
    DOMConversionMap,
    DOMConversionOutput, ElementFormatType,
    LexicalNode,
    ParagraphNode,
    SerializedParagraphNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";


export type SerializedCustomParagraphNode = Spread<{
    id: string;
}, SerializedParagraphNode>

export class CustomParagraphNode extends ParagraphNode {
    __id: string = '';

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

    static clone(node: CustomParagraphNode): CustomParagraphNode {
        const newNode = new CustomParagraphNode(node.__key);
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

    exportJSON(): SerializedCustomParagraphNode {
        return {
            ...super.exportJSON(),
            type: 'custom-paragraph',
            version: 1,
            id: this.__id,
        };
    }

    static importJSON(serializedNode: SerializedCustomParagraphNode): CustomParagraphNode {
        const node = $createCustomParagraphNode();
        node.setId(serializedNode.id);
        return node;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            p(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        const node = $createCustomParagraphNode();
                        if (element.style) {
                            node.setFormat(element.style.textAlign as ElementFormatType);
                            const indent = parseInt(element.style.textIndent, 10) / 20;
                            if (indent > 0) {
                                node.setIndent(indent);
                            }
                        }

                        if (element.id) {
                            node.setId(element.id);
                        }

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