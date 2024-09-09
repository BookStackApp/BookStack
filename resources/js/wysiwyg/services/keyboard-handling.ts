import {EditorUiContext} from "../ui/framework/core";
import {
    $isDecoratorNode,
    COMMAND_PRIORITY_LOW,
    KEY_BACKSPACE_COMMAND,
    KEY_DELETE_COMMAND,
    LexicalEditor
} from "lexical";
import {$isImageNode} from "../nodes/image";
import {$isMediaNode} from "../nodes/media";
import {getLastSelection} from "../utils/selection";

function deleteSingleSelectedNode(editor: LexicalEditor) {
    const selectionNodes = getLastSelection(editor)?.getNodes() || [];
    if (selectionNodes.length === 1) {
        const node = selectionNodes[0];
        if ($isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node)) {
            editor.update(() => {
                node.remove();
            });
        }
    }
}

export function registerKeyboardHandling(context: EditorUiContext): () => void {
    const unregisterBackspace = context.editor.registerCommand(KEY_BACKSPACE_COMMAND, (): boolean => {
        deleteSingleSelectedNode(context.editor);
        return false;
    }, COMMAND_PRIORITY_LOW);

    const unregisterDelete = context.editor.registerCommand(KEY_DELETE_COMMAND, (): boolean => {
        deleteSingleSelectedNode(context.editor);
        return false;
    }, COMMAND_PRIORITY_LOW);

    return () => {
          unregisterBackspace();
          unregisterDelete();
    };
}