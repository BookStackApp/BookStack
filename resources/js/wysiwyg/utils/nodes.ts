import {
    $createParagraphNode,
    $getRoot,
    $isDecoratorNode,
    $isElementNode, $isRootNode,
    $isTextNode,
    ElementNode,
    LexicalEditor,
    LexicalNode
} from "lexical";
import {LexicalNodeMatcher} from "../nodes";
import {$generateNodesFromDOM} from "@lexical/html";
import {htmlToDom} from "./dom";
import {NodeHasAlignment, NodeHasInset} from "lexical/nodes/common";
import {$findMatchingParent} from "@lexical/utils";

function wrapTextNodes(nodes: LexicalNode[]): LexicalNode[] {
    return nodes.map(node => {
        if ($isTextNode(node)) {
            const paragraph = $createParagraphNode();
            paragraph.append(node);
            return paragraph;
        }
        return node;
    });
}

export function $htmlToBlockNodes(editor: LexicalEditor, html: string): LexicalNode[] {
    const dom = htmlToDom(html);
    const nodes = $generateNodesFromDOM(editor, dom);
    return wrapTextNodes(nodes);
}

export function $getParentOfType(node: LexicalNode, matcher: LexicalNodeMatcher): LexicalNode | null {
    for (const parent of node.getParents()) {
        if (matcher(parent)) {
            return parent;
        }
    }

    return null;
}

export function $getAllNodesOfType(matcher: LexicalNodeMatcher, root?: ElementNode): LexicalNode[] {
    if (!root) {
        root = $getRoot();
    }

    const matches = [];

    for (const child of root.getChildren()) {
        if (matcher(child)) {
            matches.push(child);
        }

        if ($isElementNode(child)) {
            matches.push(...$getAllNodesOfType(matcher, child));
        }
    }

    return matches;
}

/**
 * Get the nearest root/block level node for the given position.
 */
export function $getNearestBlockNodeForCoords(editor: LexicalEditor, x: number, y: number): LexicalNode | null {
    // TODO - Take into account x for floated blocks?
    const rootNodes = $getRoot().getChildren();
    for (const node of rootNodes) {
        const nodeDom = editor.getElementByKey(node.__key);
        if (!nodeDom) {
            continue;
        }

        const bounds = nodeDom.getBoundingClientRect();
        if (y <= bounds.bottom) {
            return node;
        }
    }

    return null;
}

export function $getNearestNodeBlockParent(node: LexicalNode): LexicalNode|null {
    const isBlockNode = (node: LexicalNode): boolean => {
        return ($isElementNode(node) || $isDecoratorNode(node)) && !node.isInline() && !$isRootNode(node);
    };

    if (isBlockNode(node)) {
        return node;
    }

    return $findMatchingParent(node, isBlockNode);
}

export function nodeHasAlignment(node: object): node is NodeHasAlignment {
    return '__alignment' in node;
}

export function nodeHasInset(node: object): node is NodeHasInset {
    return '__inset' in node;
}