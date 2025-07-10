import {
    $createNodeSelection,
    $createParagraphNode, $createRangeSelection,
    $getRoot,
    $getSelection, $isBlockElementNode, $isDecoratorNode,
    $isElementNode, $isParagraphNode,
    $isTextNode,
    $setSelection,
    BaseSelection, DecoratorNode,
    ElementNode, LexicalEditor,
    LexicalNode,
    TextFormatType, TextNode
} from "lexical";
import {$getNearestBlockElementAncestorOrThrow} from "@lexical/utils";
import {LexicalElementNodeCreator, LexicalNodeMatcher} from "../nodes";
import {$setBlocksType} from "@lexical/selection";

import {$getNearestNodeBlockParent, $getParentOfType, nodeHasAlignment} from "./nodes";
import {CommonBlockAlignment} from "lexical/nodes/common";

const lastSelectionByEditor = new WeakMap<LexicalEditor, BaseSelection|null>;

export function getLastSelection(editor: LexicalEditor): BaseSelection|null {
    return lastSelectionByEditor.get(editor) || null;
}

export function setLastSelection(editor: LexicalEditor, selection: BaseSelection|null): void {
    lastSelectionByEditor.set(editor, selection);
}

export function $selectionContainsNodeType(selection: BaseSelection | null, matcher: LexicalNodeMatcher): boolean {
    return $getNodeFromSelection(selection, matcher) !== null;
}

export function $getNodeFromSelection(selection: BaseSelection | null, matcher: LexicalNodeMatcher): LexicalNode | null {
    if (!selection) {
        return null;
    }

    for (const node of selection.getNodes()) {
        if (matcher(node)) {
            return node;
        }

        const matchedParent = $getParentOfType(node, matcher);
        if (matchedParent) {
            return matchedParent;
        }
    }

    return null;
}

export function $getTextNodeFromSelection(selection: BaseSelection | null): TextNode|null {
    return $getNodeFromSelection(selection, $isTextNode) as TextNode|null;
}

export function $selectionContainsTextFormat(selection: BaseSelection | null, format: TextFormatType): boolean {
    if (!selection) {
        return false;
    }

    // Check text nodes
    const nodes = selection.getNodes();
    for (const node of nodes) {
        if ($isTextNode(node) && node.hasFormat(format)) {
            return true;
        }
    }

    // If we're in an empty paragraph, check the paragraph format
    if (nodes.length === 1 && $isParagraphNode(nodes[0]) && nodes[0].hasTextFormat(format)) {
        return true;
    }

    return false;
}

export function $toggleSelectionBlockNodeType(matcher: LexicalNodeMatcher, creator: LexicalElementNodeCreator) {
    const selection = $getSelection();
    const blockElement = selection ? $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]) : null;
    if (selection && matcher(blockElement)) {
        $setBlocksType(selection, $createParagraphNode);
    } else {
        $setBlocksType(selection, creator);
    }
}

export function $insertNewBlockNodeAtSelection(node: LexicalNode, insertAfter: boolean = true) {
    $insertNewBlockNodesAtSelection([node], insertAfter);
}

export function $insertNewBlockNodesAtSelection(nodes: LexicalNode[], insertAfter: boolean = true) {
    const selectionNodes = $getSelection()?.getNodes() || [];
    const blockElement = selectionNodes.length > 0 ? $getNearestNodeBlockParent(selectionNodes[0]) : null;

    if (blockElement) {
        if (insertAfter) {
            for (let i = nodes.length - 1; i >= 0; i--) {
                blockElement.insertAfter(nodes[i]);
            }
        } else {
            for (const node of nodes) {
                blockElement.insertBefore(node);
            }
        }
    } else {
        $getRoot().append(...nodes);
    }
}

export function $selectSingleNode(node: LexicalNode) {
    const nodeSelection = $createNodeSelection();
    nodeSelection.add(node.getKey());
    $setSelection(nodeSelection);
}

function getFirstTextNodeInNodes(nodes: LexicalNode[]): TextNode|null {
    for (const node of nodes) {
        if ($isTextNode(node)) {
            return node;
        }

        if ($isElementNode(node)) {
            const children = node.getChildren();
            const textNode = getFirstTextNodeInNodes(children);
            if (textNode !== null) {
                return textNode;
            }
        }
    }

    return null;
}

function getLastTextNodeInNodes(nodes: LexicalNode[]): TextNode|null {
    const revNodes = [...nodes].reverse();
    for (const node of revNodes) {
        if ($isTextNode(node)) {
            return node;
        }

        if ($isElementNode(node)) {
            const children = [...node.getChildren()].reverse();
            const textNode = getLastTextNodeInNodes(children);
            if (textNode !== null) {
                return textNode;
            }
        }
    }

    return null;
}

export function $selectNodes(nodes: LexicalNode[]) {
    if (nodes.length === 0) {
        return;
    }

    const selection = $createRangeSelection();
    const firstText = getFirstTextNodeInNodes(nodes);
    const lastText = getLastTextNodeInNodes(nodes);
    if (firstText && lastText) {
        selection.setTextNodeRange(firstText, 0, lastText, lastText.getTextContentSize() || 0)
        $setSelection(selection);
    }
}

export function $toggleSelection(editor: LexicalEditor) {
    const lastSelection = getLastSelection(editor);

    if (lastSelection) {
        window.requestAnimationFrame(() => {
            editor.update(() => {
                $setSelection(lastSelection.clone());
            })
        });
    }
}

export function $selectionContainsNode(selection: BaseSelection | null, node: LexicalNode): boolean {
    if (!selection) {
        return false;
    }

    const key = node.getKey();
    for (const node of selection.getNodes()) {
        if (node.getKey() === key) {
            return true;
        }
    }

    return false;
}

export function $selectionContainsAlignment(selection: BaseSelection | null, alignment: CommonBlockAlignment): boolean {

    const nodes = [
        ...(selection?.getNodes() || []),
        ...$getBlockElementNodesInSelection(selection)
    ];
    for (const node of nodes) {
        if (nodeHasAlignment(node) && node.getAlignment() === alignment) {
            return true;
        }
    }

    return false;
}

export function $selectionContainsDirection(selection: BaseSelection | null, direction: 'rtl'|'ltr'): boolean {

    const nodes = [
        ...(selection?.getNodes() || []),
        ...$getBlockElementNodesInSelection(selection)
    ];

    for (const node of nodes) {
        if ($isBlockElementNode(node) && node.getDirection() === direction) {
            return true;
        }
    }

    return false;
}

export function $getBlockElementNodesInSelection(selection: BaseSelection | null): ElementNode[] {
    if (!selection) {
        return [];
    }

    const blockNodes: Map<string, ElementNode> = new Map();
    for (const node of selection.getNodes()) {
        const blockElement = $getNearestNodeBlockParent(node);
        if ($isElementNode(blockElement)) {
            blockNodes.set(blockElement.getKey(), blockElement);
        }
    }

    return Array.from(blockNodes.values());
}

export function $getDecoratorNodesInSelection(selection: BaseSelection | null): DecoratorNode<any>[] {
    if (!selection) {
        return [];
    }

    return selection.getNodes().filter(node => $isDecoratorNode(node));
}