import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    SerializedElementNode,
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";

export class HorizontalRuleNode extends ElementNode {

    static getType() {
        return 'horizontal-rule';
    }

    static clone(node: HorizontalRuleNode): HorizontalRuleNode {
        return new HorizontalRuleNode(node.__key);
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        return document.createElement('hr');
    }

    updateDOM(prevNode: unknown, dom: HTMLElement) {
        return false;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            hr(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        return {
                            node: new HorizontalRuleNode(),
                        };
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedElementNode {
        return {
            ...super.exportJSON(),
            type: 'horizontal-rule',
            version: 1,
        };
    }

    static importJSON(serializedNode: SerializedElementNode): HorizontalRuleNode {
        return $createHorizontalRuleNode();
    }

}

export function $createHorizontalRuleNode() {
    return new HorizontalRuleNode();
}

export function $isHorizontalRuleNode(node: LexicalNode | null | undefined) {
    return node instanceof HorizontalRuleNode;
}