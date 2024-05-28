import {
    $createParagraphNode,
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    ParagraphNode, SerializedElementNode, Spread
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";
import type {RangeSelection} from "lexical/LexicalSelection";

export type CalloutCategory = 'info' | 'danger' | 'warning' | 'success';

export type SerializedCalloutNode = Spread<{
    category: CalloutCategory;
}, SerializedElementNode>

export class CalloutNode extends ElementNode {

    __category: CalloutCategory = 'info';

    static getType() {
        return 'callout';
    }

    static clone(node: CalloutNode) {
        return new CalloutNode(node.__category, node.__key);
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

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const element = document.createElement('p');
        element.classList.add('callout', this.__category || '');
        return element;
    }

    updateDOM(prevNode: unknown, dom: HTMLElement) {
        // Returning false tells Lexical that this node does not need its
        // DOM element replacing with a new copy from createDOM.
        return false;
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

                            return {
                                node: new CalloutNode(category),
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
        };
    }

    static importJSON(serializedNode: SerializedCalloutNode): CalloutNode {
        return $createCalloutNode(serializedNode.category);
    }

}

export function $createCalloutNode(category: CalloutCategory = 'info') {
    return new CalloutNode(category);
}

export function $isCalloutNode(node: LexicalNode | null | undefined) {
    return node instanceof CalloutNode;
}

export function $isCalloutNodeOfCategory(node: LexicalNode | null | undefined, category: CalloutCategory = 'info') {
    return node instanceof CalloutNode && (node as CalloutNode).getCategory() === category;
}
