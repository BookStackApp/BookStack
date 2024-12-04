import {EditorUiContext} from "../ui/framework/core";
import {
    $createParagraphNode,
    $getSelection,
    $isDecoratorNode,
    COMMAND_PRIORITY_LOW,
    KEY_BACKSPACE_COMMAND,
    KEY_DELETE_COMMAND,
    KEY_ENTER_COMMAND, KEY_TAB_COMMAND,
    LexicalEditor,
    LexicalNode
} from "lexical";
import {$isImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$isMediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {getLastSelection} from "../utils/selection";
import {$getNearestNodeBlockParent} from "../utils/nodes";
import {$setInsetForSelection} from "../utils/lists";
import {$isListItemNode} from "@lexical/list";

function isSingleSelectedNode(nodes: LexicalNode[]): boolean {
    if (nodes.length === 1) {
        const node = nodes[0];
        if ($isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node)) {
            return true;
        }
    }

    return false;
}

function deleteSingleSelectedNode(editor: LexicalEditor) {
    const selectionNodes = getLastSelection(editor)?.getNodes() || [];
    if (isSingleSelectedNode(selectionNodes)) {
        editor.update(() => {
            selectionNodes[0].remove();
        });
    }
}

function insertAfterSingleSelectedNode(editor: LexicalEditor, event: KeyboardEvent|null): boolean {
    const selectionNodes = getLastSelection(editor)?.getNodes() || [];
    if (isSingleSelectedNode(selectionNodes)) {
        const node = selectionNodes[0];
        const nearestBlock = $getNearestNodeBlockParent(node) || node;
        if (nearestBlock) {
            requestAnimationFrame(() => {
                editor.update(() => {
                    const newParagraph = $createParagraphNode();
                    nearestBlock.insertAfter(newParagraph);
                    newParagraph.select();
                });
            });
            event?.preventDefault();
            return true;
        }
    }

    return false;
}

function handleInsetOnTab(editor: LexicalEditor, event: KeyboardEvent|null): boolean {
    const change = event?.shiftKey ? -40 : 40;
    const selection = $getSelection();
    const nodes = selection?.getNodes() || [];
    if (nodes.length > 1 || (nodes.length === 1 && $isListItemNode(nodes[0].getParent()))) {
        editor.update(() => {
            $setInsetForSelection(editor, change);
        });
        event?.preventDefault();
        return true;
    }

    return false;
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

    const unregisterEnter = context.editor.registerCommand(KEY_ENTER_COMMAND, (event): boolean => {
        return insertAfterSingleSelectedNode(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    const unregisterTab = context.editor.registerCommand(KEY_TAB_COMMAND, (event): boolean => {
        return handleInsetOnTab(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    return () => {
        unregisterBackspace();
        unregisterDelete();
        unregisterEnter();
        unregisterTab();
    };
}