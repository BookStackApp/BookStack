import {$createParagraphNode, ElementNode} from 'lexical';

export class Callout extends ElementNode {

    __category = 'info';

    static getType() {
        return 'callout';
    }

    static clone(node) {
        return new Callout(node.__category, node.__key);
    }

    constructor(category, key) {
        super(key);
        this.__category = category;
    }

    createDOM(_config, _editor) {
        const element = document.createElement('p');
        element.classList.add('callout', this.__category || '');
        return element;
    }

    updateDOM(prevNode, dom) {
        // Returning false tells Lexical that this node does not need its
        // DOM element replacing with a new copy from createDOM.
        return false;
    }

    insertNewAfter(selection, restoreSelection) {
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

    static importDOM() {
        return {
            p: node => {
                if (node.classList.contains('callout')) {
                    return {
                        conversion: element => {
                            let category = 'info';
                            const categories = ['info', 'success', 'warning', 'danger'];

                            for (const c of categories) {
                                if (element.classList.contains(c)) {
                                    category = c;
                                    break;
                                }
                            }

                            return {
                                node: new Callout(category),
                            };
                        },
                        priority: 3,
                    };
                }
                return null;
            },
        };
    }

    exportJSON() {
        return {
            ...super.exportJSON(),
            type: 'callout',
            version: 1,
            category: this.__category,
        };
    }

    static importJSON(serializedNode) {
        return $createCalloutNode(serializedNode.category);
    }

}

export function $createCalloutNode(category = 'info') {
    return new Callout(category);
}

export function $isCalloutNode(node) {
    return node instanceof Callout;
}
