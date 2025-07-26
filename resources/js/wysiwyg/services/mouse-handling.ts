import {EditorUiContext} from "../ui/framework/core";
import {
    $createParagraphNode, $getRoot,
    $getSelection,
    $isDecoratorNode, CLICK_COMMAND,
    COMMAND_PRIORITY_LOW, KEY_ARROW_DOWN_COMMAND, KEY_ARROW_UP_COMMAND,
    KEY_BACKSPACE_COMMAND,
    KEY_DELETE_COMMAND,
    KEY_ENTER_COMMAND, KEY_TAB_COMMAND,
    LexicalEditor,
    LexicalNode
} from "lexical";
import {$isImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$isMediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {getLastSelection} from "../utils/selection";
import {$getNearestNodeBlockParent, $getParentOfType, $selectOrCreateAdjacent} from "../utils/nodes";
import {$setInsetForSelection} from "../utils/lists";
import {$isListItemNode} from "@lexical/list";
import {$isDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {$isDiagramNode} from "../utils/diagrams";
import {$isTableNode} from "@lexical/table";

function isHardToEscapeNode(node: LexicalNode): boolean {
    return $isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node) || $isDiagramNode(node) || $isTableNode(node);
}

function insertBelowLastNode(context: EditorUiContext, event: MouseEvent): boolean {
    const lastNode = $getRoot().getLastChild();
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
            $getRoot().append(newNode);
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