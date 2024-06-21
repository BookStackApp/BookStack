import {
    $createParagraphNode, $getRoot,
    $getSelection,
    $isTextNode,
    BaseSelection, ElementNode,
    LexicalEditor, LexicalNode, TextFormatType
} from "lexical";
import {LexicalElementNodeCreator, LexicalNodeMatcher} from "./nodes";
import {$getNearestBlockElementAncestorOrThrow} from "@lexical/utils";
import {$setBlocksType} from "@lexical/selection";
import {$createDetailsNode} from "./nodes/details";

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

export function selectionContainsNodeType(selection: BaseSelection|null, matcher: LexicalNodeMatcher): boolean {
    return getNodeFromSelection(selection, matcher) !== null;
}

export function getNodeFromSelection(selection: BaseSelection|null, matcher: LexicalNodeMatcher): LexicalNode|null {
    if (!selection) {
        return null;
    }

    for (const node of selection.getNodes()) {
        if (matcher(node)) {
            return node;
        }

        for (const parent of node.getParents()) {
            if (matcher(parent)) {
                return parent;
            }
        }
    }

    return null;
}

export function selectionContainsTextFormat(selection: BaseSelection|null, format: TextFormatType): boolean {
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

export function toggleSelectionBlockNodeType(editor: LexicalEditor, matcher: LexicalNodeMatcher, creator: LexicalElementNodeCreator) {
    editor.update(() => {
        const selection = $getSelection();
        const blockElement = selection ? $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]) : null;
        if (selection && matcher(blockElement)) {
            $setBlocksType(selection, $createParagraphNode);
        } else {
            $setBlocksType(selection, creator);
        }
    });
}

export function insertNewBlockNodeAtSelection(node: LexicalNode) {
    const selection = $getSelection();
    const blockElement = selection ? $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]) : null;

    if (blockElement) {
        blockElement.insertAfter(node);
    } else {
        $getRoot().append(node);
    }
}