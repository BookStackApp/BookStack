import {EditorUiContext} from "../ui/framework/core";
import {
    $createParagraphNode,
    $getSelection,
    $isDecoratorNode,
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
import {$unwrapDetailsNode} from "../utils/details";

function isSingleSelectedNode(nodes: LexicalNode[]): boolean {
    if (nodes.length === 1) {
        const node = nodes[0];
        if ($isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node) || $isDiagramNode(node)) {
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
 * Insert a new empty node before/after the selection if the selection contains a single
 * selected node (like image, media etc...).
 */
function insertAdjacentToSingleSelectedNode(editor: LexicalEditor, event: KeyboardEvent|null): boolean {
    const selectionNodes = getLastSelection(editor)?.getNodes() || [];
    if (isSingleSelectedNode(selectionNodes)) {
        const node = selectionNodes[0];
        const nearestBlock = $getNearestNodeBlockParent(node) || node;
        const insertBefore = event?.shiftKey === true;
        if (nearestBlock) {
            requestAnimationFrame(() => {
                editor.update(() => {
                    const newParagraph = $createParagraphNode();
                    if (insertBefore) {
                        nearestBlock.insertBefore(newParagraph);
                    } else {
                        nearestBlock.insertAfter(newParagraph);
                    }
                    newParagraph.select();
                });
            });
            event?.preventDefault();
            return true;
        }
    }

    return false;
}

function focusAdjacentOrInsertForSingleSelectNode(editor: LexicalEditor, event: KeyboardEvent|null, after: boolean = true): boolean {
    const selectionNodes = getLastSelection(editor)?.getNodes() || [];
    if (!isSingleSelectedNode(selectionNodes)) {
        return false;
    }

    event?.preventDefault();
    const node = selectionNodes[0];
    editor.update(() => {
        $selectOrCreateAdjacent(node, after);
    });

    return true;
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

function unwrapDetailsNode(context: EditorUiContext, event: KeyboardEvent): boolean {
    const selection = $getSelection();
    const nodes = selection?.getNodes() || [];

    if (nodes.length !== 1) {
        return false;
    }

    const selectedNearestBlock = $getNearestNodeBlockParent(nodes[0]);
    if (!selectedNearestBlock) {
        return false;
    }

    const selectedParentBlock = selectedNearestBlock.getParent();
    const selectRange = selection?.getStartEndPoints();

    if (selectRange && $isDetailsNode(selectedParentBlock) && selectRange[0].offset === 0 && selectedNearestBlock.getIndexWithinParent() === 0) {
        event.preventDefault();
        context.editor.update(() => {
            $unwrapDetailsNode(selectedParentBlock);
            selectedNearestBlock.selectStart();
            context.manager.triggerLayoutUpdate();
        });
        return true;
    }

    return false;
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
    const unregisterBackspace = context.editor.registerCommand(KEY_BACKSPACE_COMMAND, (event): boolean => {
        deleteSingleSelectedNode(context.editor);
        return unwrapDetailsNode(context, event);
    }, COMMAND_PRIORITY_LOW);

    const unregisterDelete = context.editor.registerCommand(KEY_DELETE_COMMAND, (): boolean => {
        deleteSingleSelectedNode(context.editor);
        return false;
    }, COMMAND_PRIORITY_LOW);

    const unregisterEnter = context.editor.registerCommand(KEY_ENTER_COMMAND, (event): boolean => {
        return insertAdjacentToSingleSelectedNode(context.editor, event)
            || moveAfterDetailsOnEmptyLine(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    const unregisterTab = context.editor.registerCommand(KEY_TAB_COMMAND, (event): boolean => {
        return handleInsetOnTab(context.editor, event);
    }, COMMAND_PRIORITY_LOW);

    const unregisterUp = context.editor.registerCommand(KEY_ARROW_UP_COMMAND, (event): boolean => {
        return focusAdjacentOrInsertForSingleSelectNode(context.editor, event, false);
    }, COMMAND_PRIORITY_LOW);

    const unregisterDown = context.editor.registerCommand(KEY_ARROW_DOWN_COMMAND, (event): boolean => {
        return insertAfterDetails(context.editor, event)
            || focusAdjacentOrInsertForSingleSelectNode(context.editor, event, true)
    }, COMMAND_PRIORITY_LOW);

    return () => {
        unregisterBackspace();
        unregisterDelete();
        unregisterEnter();
        unregisterTab();
        unregisterUp();
        unregisterDown();
    };
}