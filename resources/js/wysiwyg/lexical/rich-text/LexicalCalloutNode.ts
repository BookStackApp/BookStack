import {
    $createParagraphNode,
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    ParagraphNode, Spread
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";
import type {RangeSelection} from "lexical/LexicalSelection";
import {
    CommonBlockAlignment, commonPropertiesDifferent, deserializeCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "lexical/nodes/common";
import {SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";

export type CalloutCategory = 'info' | 'danger' | 'warning' | 'success';

export type SerializedCalloutNode = Spread<{
    category: CalloutCategory;
}, SerializedCommonBlockNode>

export class CalloutNode extends ElementNode {
    __id: string = '';
    __category: CalloutCategory = 'info';
    __alignment: CommonBlockAlignment = '';
    __inset: number = 0;

    static getType() {
        return 'callout';
    }

    static clone(node: CalloutNode) {
        const newNode = new CalloutNode(node.__category, node.__key);
        newNode.__id = node.__id;
        newNode.__alignment = node.__alignment;
        newNode.__inset = node.__inset;
        return newNode;
    }

    constructor(category: CalloutCategory, key?: string) {
        super(key);
        this.__category = category;
    }

    setCategory(category: CalloutCategory) {
        const self = this.getWritable();
        self.__category = category;
    }

    getCategory(): CalloutCategory {
        const self = this.getLatest();
        return self.__category;
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

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const element = document.createElement('p');
        element.classList.add('callout', this.__category || '');
        updateElementWithCommonBlockProps(element, this);
        return element;
    }

    updateDOM(prevNode: CalloutNode): boolean {
        return prevNode.__category !== this.__category ||
            commonPropertiesDifferent(prevNode, this);
    }

    insertNewAfter(selection: RangeSelection, restoreSelection?: boolean): CalloutNode|ParagraphNode {
        const anchorOffset = selection ? selection.anchor.offset : 0;
        const newElement = anchorOffset === this.getTextContentSize() || !selection
            ? $createParagraphNode() : $createCalloutNode(this.__category);

        newElement.setDirection(this.getDirection());
        this.insertAfter(newElement, restoreSelection);

        if (anchorOffset === 0 && !this.isEmpty() && selection) {
            const paragraph = $createParagraphNode();
            paragraph.select();
            this.replace(paragraph, true);
        }

        return newElement;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            p(node: HTMLElement): DOMConversion|null {
                if (node.classList.contains('callout')) {
                    return {
                        conversion: (element: HTMLElement): DOMConversionOutput|null => {
                            let category: CalloutCategory = 'info';
                            const categories: CalloutCategory[] = ['info', 'success', 'warning', 'danger'];

                            for (const c of categories) {
                                if (element.classList.contains(c)) {
                                    category = c;
                                    break;
                                }
                            }

                            const node = new CalloutNode(category);
                            setCommonBlockPropsFromElement(element, node);

                            return {
                                node,
                            };
                        },
                        priority: 3,
                    };
                }
                return null;
            },
        };
    }

    exportJSON(): SerializedCalloutNode {
        return {
            ...super.exportJSON(),
            type: 'callout',
            version: 1,
            category: this.__category,
            id: this.__id,
            alignment: this.__alignment,
            inset: this.__inset,
        };
    }

    static importJSON(serializedNode: SerializedCalloutNode): CalloutNode {
        const node = $createCalloutNode(serializedNode.category);
        deserializeCommonBlockNode(serializedNode, node);
        return node;
    }

}

export function $createCalloutNode(category: CalloutCategory = 'info') {
    return new CalloutNode(category);
}

export function $isCalloutNode(node: LexicalNode | null | undefined): node is CalloutNode {
    return node instanceof CalloutNode;
}

export function $isCalloutNodeOfCategory(node: LexicalNode | null | undefined, category: CalloutCategory = 'info') {
    return node instanceof CalloutNode && (node as CalloutNode).getCategory() === category;
}
