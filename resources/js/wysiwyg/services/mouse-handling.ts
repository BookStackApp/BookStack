import {EditorUiContext} from "../ui/framework/core";
import {
    $createParagraphNode, $getNearestNodeFromDOMNode, $getRoot,
    $isDecoratorNode, CLICK_COMMAND,
    COMMAND_PRIORITY_LOW, ElementNode,
    LexicalNode
} from "lexical";
import {$isImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$isMediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {$isDiagramNode} from "../utils/diagrams";
import {$isTableNode} from "@lexical/table";
import {$isDetailsNode} from "@lexical/rich-text/LexicalDetailsNode";

function isHardToEscapeNode(node: LexicalNode): boolean {
    return $isDecoratorNode(node)
        || $isImageNode(node)
        || $isMediaNode(node)
        || $isDiagramNode(node)
        || $isTableNode(node)
        || $isDetailsNode(node);
}

function $getContextNode(event: MouseEvent): ElementNode {
    if (event.target instanceof HTMLElement) {
        const nearestDetails = event.target.closest('details');
        if (nearestDetails) {
            const detailsNode = $getNearestNodeFromDOMNode(nearestDetails);
            if ($isDetailsNode(detailsNode)) {
                return detailsNode;
            }
        }
    }
    return $getRoot();
}

function insertBelowLastNode(context: EditorUiContext, event: MouseEvent): boolean {
    const contextNode = $getContextNode(event);
    const lastNode = contextNode.getLastChild();
    if (!lastNode || !isHardToEscapeNode(lastNode)) {
        return false;
    }

    const lastNodeDom = context.editor.getElementByKey(lastNode.getKey());
    if (!lastNodeDom) {
        return false;
    }

    const nodeBounds = lastNodeDom.getBoundingClientRect();
    const isClickBelow = event.clientY > nodeBounds.bottom;
    if (isClickBelow) {
        context.editor.update(() => {
            const newNode = $createParagraphNode();
            contextNode.append(newNode);
            newNode.select();
        });
        return true;
    }

    return false;
}

export function registerMouseHandling(context: EditorUiContext): () => void {
    const unregisterClick = context.editor.registerCommand(CLICK_COMMAND, (event): boolean => {
        insertBelowLastNode(context, event);
        return false;
    }, COMMAND_PRIORITY_LOW);


    return () => {
        unregisterClick();
    };
}