import {$createTextNode, $getSelection, BaseSelection, LexicalEditor, TextNode} from "lexical";
import {$getBlockElementNodesInSelection, $selectNodes, $toggleSelection} from "./selection";
import {$sortNodes, nodeHasInset} from "./nodes";
import {$createListItemNode, $createListNode, $isListItemNode, $isListNode, ListItemNode} from "@lexical/list";


export function $nestListItem(node: ListItemNode): ListItemNode {
    const list = node.getParent();
    if (!$isListNode(list)) {
        return node;
    }

    const nodeChildList = node.getChildren().filter(n => $isListNode(n))[0] || null;
    const nodeChildItems = nodeChildList?.getChildren() || [];

    const listItems = list.getChildren() as ListItemNode[];
    const nodeIndex = listItems.findIndex((n) => n.getKey() === node.getKey());
    const isFirst = nodeIndex === 0;

    const newListItem = $createListItemNode();
    const newList = $createListNode(list.getListType());
    newList.append(newListItem);
    newListItem.append(...node.getChildren());

    if (isFirst) {
        node.append(newList);
    } else  {
        const prevListItem = listItems[nodeIndex - 1];
        prevListItem.append(newList);
        node.remove();
    }

    if (nodeChildList) {
        for (const child of nodeChildItems) {
            newListItem.insertAfter(child);
        }
        nodeChildList.remove();
    }

    return newListItem;
}

export function $unnestListItem(node: ListItemNode): ListItemNode {
    const list = node.getParent();
    const parentListItem = list?.getParent();
    const outerList = parentListItem?.getParent();
    if (!$isListNode(list) || !$isListNode(outerList) || !$isListItemNode(parentListItem)) {
        return node;
    }

    const laterSiblings = node.getNextSiblings();
    parentListItem.insertAfter(node);
    if (list.getChildren().length === 0) {
        list.remove();
    }

    if (laterSiblings.length > 0) {
        const childList = $createListNode(list.getListType());
        childList.append(...laterSiblings);
        node.append(childList);
    }

    if (list.getChildrenSize() === 0) {
        list.remove();
    }

    if (parentListItem.getChildren().length === 0) {
        parentListItem.remove();
    }

    return node;
}

function getListItemsForSelection(selection: BaseSelection|null): (ListItemNode|null)[] {
    const nodes = selection?.getNodes() || [];
    let [start, end] = selection?.getStartEndPoints() || [null, null];

    // Ensure we ignore parent list items of the top-most list item since,
    // although technically part of the selection, from a user point of
    // view the selection does not spread to encompass this outer element.
    const itemsToIgnore: Set<string> = new Set();
    if (selection && start) {
        if (selection.isBackward() && end) {
            [end, start] = [start, end];
        }

        const startParents = start.getNode().getParents();
        let foundList = false;
        for (const parent of startParents) {
            if ($isListItemNode(parent)) {
                if (foundList) {
                    itemsToIgnore.add(parent.getKey());
                } else {
                    foundList = true;
                }
            }
        }
    }

    const listItemNodes = [];
    outer: for (const node of nodes) {
        if ($isListItemNode(node)) {
            if (!itemsToIgnore.has(node.getKey())) {
                listItemNodes.push(node);
            }
            continue;
        }

        const parents = node.getParents();
        for (const parent of parents) {
            if ($isListItemNode(parent)) {
                if (!itemsToIgnore.has(parent.getKey())) {
                    listItemNodes.push(parent);
                }
                continue outer;
            }
        }

        listItemNodes.push(null);
    }

    return listItemNodes;
}

function $reduceDedupeListItems(listItems: (ListItemNode|null)[]): ListItemNode[] {
    const listItemMap: Record<string, ListItemNode> = {};

    for (const item of listItems) {
        if (item === null) {
            continue;
        }

        const key = item.getKey();
        if (typeof listItemMap[key] === 'undefined') {
            listItemMap[key] = item;
        }
    }

    const items = Object.values(listItemMap);
    return $sortNodes(items) as ListItemNode[];
}

export function $setInsetForSelection(editor: LexicalEditor, change: number): void {
    const selection = $getSelection();
    const selectionBounds = selection?.getStartEndPoints();
    const listItemsInSelection = getListItemsForSelection(selection);
    const isListSelection = listItemsInSelection.length > 0 && !listItemsInSelection.includes(null);

    if (isListSelection) {
        const alteredListItems = [];
        const listItems = $reduceDedupeListItems(listItemsInSelection);
        if (change > 0) {
            for (const listItem of listItems) {
                alteredListItems.push($nestListItem(listItem));
            }
        } else if (change < 0) {
            for (const listItem of [...listItems].reverse()) {
                alteredListItems.push($unnestListItem(listItem));
            }
            alteredListItems.reverse();
        }

        if (alteredListItems.length === 1 && selectionBounds) {
            // Retain selection range if moving just one item
            const listItem = alteredListItems[0] as ListItemNode;
            let child = listItem.getChildren()[0] as TextNode;
            if (!child) {
                child = $createTextNode('');
                listItem.append(child);
            }
            child.select(selectionBounds[0].offset, selectionBounds[1].offset);
        } else {
            $selectNodes(alteredListItems);
        }

        return;
    }

    const elements = $getBlockElementNodesInSelection(selection);
    for (const node of elements) {
        if (nodeHasInset(node)) {
            const currentInset = node.getInset();
            const newInset = Math.min(Math.max(currentInset + change, 0), 500);
            node.setInset(newInset)
        }
    }

    $toggleSelection(editor);
}