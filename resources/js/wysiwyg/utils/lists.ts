import {$getSelection, BaseSelection, LexicalEditor} from "lexical";
import {$getBlockElementNodesInSelection, $selectNodes, $toggleSelection} from "./selection";
import {nodeHasInset} from "./nodes";
import {$createListItemNode, $createListNode, $isListItemNode, $isListNode, ListItemNode} from "@lexical/list";


export function $nestListItem(node: ListItemNode): ListItemNode {
    const list = node.getParent();
    if (!$isListNode(list)) {
        return node;
    }

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

    return newListItem;
}

export function $unnestListItem(node: ListItemNode): ListItemNode {
    const list = node.getParent();
    const parentListItem = list?.getParent();
    const outerList = parentListItem?.getParent();
    if (!$isListNode(list) || !$isListNode(outerList) || !$isListItemNode(parentListItem)) {
        return node;
    }

    parentListItem.insertAfter(node);
    if (list.getChildren().length === 0) {
        list.remove();
    }

    if (parentListItem.getChildren().length === 0) {
        parentListItem.remove();
    }

    return node;
}

function getListItemsForSelection(selection: BaseSelection|null): (ListItemNode|null)[] {
    const nodes = selection?.getNodes() || [];
    const listItemNodes = [];

    outer: for (const node of nodes) {
        if ($isListItemNode(node)) {
            listItemNodes.push(node);
            continue;
        }

        const parents = node.getParents();
        for (const parent of parents) {
            if ($isListItemNode(parent)) {
                listItemNodes.push(parent);
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

    return Object.values(listItemMap);
}

export function $setInsetForSelection(editor: LexicalEditor, change: number): void {
    const selection = $getSelection();
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

        $selectNodes(alteredListItems);
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