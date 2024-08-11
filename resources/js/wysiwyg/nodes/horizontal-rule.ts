import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    SerializedElementNode, Spread,
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";

export type SerializedHorizontalRuleNode = Spread<{
    id: string;
}, SerializedElementNode>

export class HorizontalRuleNode extends ElementNode {
    __id: string = '';

    static getType() {
        return 'horizontal-rule';
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    static clone(node: HorizontalRuleNode): HorizontalRuleNode {
        const newNode = new HorizontalRuleNode(node.__key);
        newNode.__id = node.__id;
        return newNode;
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor): HTMLElement {
        const el = document.createElement('hr');
        if (this.__id) {
            el.setAttribute('id', this.__id);
        }

        return el;
    }

    updateDOM(prevNode: HorizontalRuleNode, dom: HTMLElement) {
        return prevNode.__id !== this.__id;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            hr(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        const node = new HorizontalRuleNode();
                        if (element.id) {
                            node.setId(element.id);
                        }

                        return {node};
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedHorizontalRuleNode {
        return {
            ...super.exportJSON(),
            type: 'horizontal-rule',
            version: 1,
            id: this.__id,
        };
    }

    static importJSON(serializedNode: SerializedHorizontalRuleNode): HorizontalRuleNode {
        const node = $createHorizontalRuleNode();
        node.setId(serializedNode.id);
        return node;
    }

}

export function $createHorizontalRuleNode(): HorizontalRuleNode {
    return new HorizontalRuleNode();
}

export function $isHorizontalRuleNode(node: LexicalNode | null | undefined): node is HorizontalRuleNode {
    return node instanceof HorizontalRuleNode;
}