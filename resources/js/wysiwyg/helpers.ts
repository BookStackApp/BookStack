import {
    $createNodeSelection,
    $createParagraphNode, $getRoot,
    $getSelection, $isElementNode,
    $isTextNode, $setSelection,
    BaseSelection, ElementFormatType, ElementNode, LexicalEditor,
    LexicalNode, TextFormatType
} from "lexical";
import {LexicalElementNodeCreator, LexicalNodeMatcher} from "./nodes";
import {$findMatchingParent, $getNearestBlockElementAncestorOrThrow} from "@lexical/utils";
import {$setBlocksType} from "@lexical/selection";
import {$createCustomParagraphNode} from "./nodes/custom-paragraph";
import {$generateNodesFromDOM} from "@lexical/html";

export function el(tag: string, attrs: Record<string, string|null> = {}, children: (string|HTMLElement)[] = []): HTMLElement {
    const el = document.createElement(tag);
    const attrKeys = Object.keys(attrs);
    for (const attr of attrKeys) {
        if (attrs[attr] !== null) {
            el.setAttribute(attr, attrs[attr] as string);
        }
    }

    for (const child of children) {
        if (typeof child === 'string') {
            el.append(document.createTextNode(child));
        } else {
            el.append(child);
        }
    }

    return el;
}

function htmlToDom(html: string): Document {
    const parser = new DOMParser();
    return parser.parseFromString(html, 'text/html');
}

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

export function $selectionContainsNodeType(selection: BaseSelection|null, matcher: LexicalNodeMatcher): boolean {
    return $getNodeFromSelection(selection, matcher) !== null;
}

export function $getNodeFromSelection(selection: BaseSelection|null, matcher: LexicalNodeMatcher): LexicalNode|null {
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

export function $getParentOfType(node: LexicalNode, matcher: LexicalNodeMatcher): LexicalNode|null {
    for (const parent of node.getParents()) {
        if (matcher(parent)) {
            return parent;
        }
    }

    return null;
}

export function $selectionContainsTextFormat(selection: BaseSelection|null, format: TextFormatType): boolean {
    if (!selection) {
        return false;
    }

    for (const node of selection.getNodes()) {
        if ($isTextNode(node) && node.hasFormat(format)) {
            return true;
        }
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
    const selection = $getSelection();
    const blockElement = selection ? $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]) : null;

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

export function $selectionContainsNode(selection: BaseSelection|null, node: LexicalNode): boolean {
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

export function $selectionContainsElementFormat(selection: BaseSelection|null, format: ElementFormatType): boolean {
    const nodes = $getBlockElementNodesInSelection(selection);
    for (const node of nodes) {
        if (node.getFormatType() === format) {
            return true;
        }
    }

    return false;
}

export function $getBlockElementNodesInSelection(selection: BaseSelection|null): ElementNode[] {
    if (!selection) {
        return [];
    }

    const blockNodes: Map<string, ElementNode> = new Map();
    for (const node of selection.getNodes()) {
        const blockElement = $findMatchingParent(node, (node) => {
            return $isElementNode(node) && !node.isInline();
        }) as ElementNode|null;

        if (blockElement) {
            blockNodes.set(blockElement.getKey(), blockElement);
        }
    }

    return Array.from(blockNodes.values());
}

/**
 * Get the nearest root/block level node for the given position.
 */
export function $getNearestBlockNodeForCoords(editor: LexicalEditor, x: number, y: number): LexicalNode|null {
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