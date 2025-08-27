import {EditorUiContext} from "../ui/framework/core";
import {
    $getSelection,
    COMMAND_PRIORITY_LOW,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {$isDetailsNode} from "@lexical/rich-text/LexicalDetailsNode";


const trackedDomNodes = new Set<HTMLElement>();

/**
 * Set a selection indicator on nodes which require it.
 * @param context
 */
function setSelectionIndicator(context: EditorUiContext): boolean {

    for (const domNode of trackedDomNodes) {
        domNode.classList.remove('selected');
        trackedDomNodes.delete(domNode);
    }

    const selection = $getSelection();
    const nodes = selection?.getNodes() || [];

    if (nodes.length === 1) {
        if ($isDetailsNode(nodes[0])) {
            const domEl = context.editor.getElementByKey(nodes[0].getKey());
            if (domEl) {
                domEl.classList.add('selected');
                trackedDomNodes.add(domEl);
            }
        }
    }

    return false;
}

export function registerSelectionHandling(context: EditorUiContext): () => void {
    const unregisterSelectionChange = context.editor.registerCommand(SELECTION_CHANGE_COMMAND, (): boolean => {
        setSelectionIndicator(context);
        return false;
    }, COMMAND_PRIORITY_LOW);


    return () => {
        unregisterSelectionChange();
    };
}