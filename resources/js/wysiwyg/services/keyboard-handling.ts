import {EditorUiContext} from "../ui/framework/core";
import {
    $createParagraphNode,
    $getSelection,
    $isDecoratorNode,
    COMMAND_PRIORITY_LOW, KEY_ARROW_DOWN_COMMAND,
    KEY_BACKSPACE_COMMAND,
    KEY_DELETE_COMMAND,
    KEY_ENTER_COMMAND, KEY_TAB_COMMAND,
    LexicalEditor,
    LexicalNode
} from "lexical";
import {$isImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$isMediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {getLastSelection} from "../utils/selection";
import {$getNearestNodeBlockParent, $getParentOfType} from "../utils/nodes";
import {$setInsetForSelection} from "../utils/lists";
import {$isListItemNode} from "@lexical/list";
import {$isDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";

function isSingleSelectedNode(nodes: LexicalNode[]): boolean {
    if (nodes.length === 1) {
        const node = nodes[0];
        if ($isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node)) {
            return true;
        }
    }

    return false;
}

/**
 * Delete the current node in the selection if the selection contains a single
 * selected node (like image, media etc...).
 */
function deleteSingleSelectedNode(editor: LexicalEditor) {
    const selectionNodes = getLastSelection(editor)?.getNodes() || [];
    if (isSingleSelectedNode(selectionNodes)) {
        editor.update(() => {
            selectionNodes[0].remove();
        });
    }
}

/**
 * Insert a new empty node after the selection if the selection contains a single
 * selected node (like image, media etc...).
 */
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

/**
 * Insert a new node after a details node, if inside a details node that's
 * the last element, and if the cursor is at the last block within the details node.
 */
function insertAfterDetails(editor: LexicalEditor, event: KeyboardEvent|null): boolean {
    const scenario = getDetailsScenario(editor);
    if (scenario === null || scenario.detailsSibling) {
        return false;
    }

    editor.update(() => {
        const newParagraph = $createParagraphNode();
        scenario.parentDetails.insertAfter(newParagraph);
        newParagraph.select();
    });
    event?.preventDefault();

    return true;
}

/**
 * If within a details block, move after it, creating a new node if required, if we're on
 * the last empty block element within the details node.
 */
function moveAfterDetailsOnEmptyLine(editor: LexicalEditor, event: KeyboardEvent|null): boolean {
    const scenario = getDetailsScenario(editor);
    if (scenario === null) {
        return false;
    }

    if (scenario.parentBlock.getTextContent() !== '') {
        return false;
    }

    event?.preventDefault()

    const nextSibling = scenario.parentDetails.getNextSibling();
    editor.update(() => {
        if (nextSibling) {
            nextSibling.selectStart();
        } else {
            const newParagraph = $createParagraphNode();
            scenario.parentDetails.insertAfter(newParagraph);
            newParagraph.select();
        }
        scenario.parentBlock.remove();
    });

    return true;
}

/**
 * Get the common nodes used for a details node scenario, relative to current selection.
 * Returns null if not found, or if the parent block is not the last in the parent details node.
 */
function getDetailsScenario(editor: LexicalEditor): {
    parentDetails: DetailsNode;
    parentBlock: LexicalNode;
    detailsSibling: LexicalNode | null
} | null {
    const selection = getLastSelection(editor);
    const firstNode = selection?.getNodes()[0];
    if (!firstNode) {
        return null;
    }

    const block = $getNearestNodeBlockParent(firstNode);
    const details = $getParentOfType(firstNode, $isDetailsNode);
    if (!$isDetailsNode(details) || block === null) {
        return null;
    }

    if (block.getKey() !== details.getLastChild()?.getKey()) {
        return null;
    }

    const nextSibling = details.getNextSibling();
    return {
        parentDetails: details,
        parentBlock: block,
        detailsSibling: nextSibling,
    }
}

function $isSingleListItem(nodes: LexicalNode[]): boolean {
    if (nodes.length !== 1) {
        return false;
    }

    const node = nodes[0];
    return $isListItemNode(node) || $isListItemNode(node.getParent());
}

/**
 * Inset the nodes within selection when a range of nodes is selected
 * or if a list node is selected.
 */
function handleInsetOnTab(editor: LexicalEditor, event: KeyboardEvent|null): boolean {
    const change = event?.shiftKey ? -40 : 40;
    const selection = $getSelection();
    const nodes = selection?.getNodes() || [];
    if (nodes.length > 1 || $isSingleListItem(nodes)) {
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
        return insertAfterSingleSelectedNode(context.editor, event)
            || moveAfterDetailsOnEmptyLine(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    const unregisterTab = context.editor.registerCommand(KEY_TAB_COMMAND, (event): boolean => {
        return handleInsetOnTab(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    const unregisterDown = context.editor.registerCommand(KEY_ARROW_DOWN_COMMAND, (event): boolean => {
        return insertAfterDetails(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    return () => {
        unregisterBackspace();
        unregisterDelete();
        unregisterEnter();
        unregisterTab();
        unregisterDown();
    };
}