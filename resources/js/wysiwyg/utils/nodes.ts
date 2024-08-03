import {$getRoot, $isTextNode, LexicalEditor, LexicalNode} from "lexical";
import {LexicalNodeMatcher} from "../nodes";
import {$createCustomParagraphNode} from "../nodes/custom-paragraph";
import {$generateNodesFromDOM} from "@lexical/html";
import {htmlToDom} from "./dom";

function wrapTextNodes(nodes: LexicalNode[]): LexicalNode[] {
    return nodes.map(node => {
        if ($isTextNode(node)) {
            const paragraph = $createCustomParagraphNode();
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