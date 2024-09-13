import {
    DOMConversionFn,
    DOMConversionMap,
    LexicalNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {$isListItemNode, ListItemNode, ListNode, ListType, SerializedListNode} from "@lexical/list";
import {$createCustomListItemNode} from "./custom-list-item";


export type SerializedCustomListNode = Spread<{
    id: string;
}, SerializedListNode>

export class CustomListNode extends ListNode {
    __id: string = '';

    static getType() {
        return 'custom-list';
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    static clone(node: CustomListNode) {
        const newNode = new CustomListNode(node.__listType, node.__start, node.__key);
        newNode.__id = node.__id;
        return newNode;
    }

    createDOM(config: EditorConfig): HTMLElement {
        const dom = super.createDOM(config);
        if (this.__id) {
            dom.setAttribute('id', this.__id);
        }

        return dom;
    }

    exportJSON(): SerializedCustomListNode {
        return {
            ...super.exportJSON(),
            type: 'custom-list',
            version: 1,
            id: this.__id,
        };
    }

    static importJSON(serializedNode: SerializedCustomListNode): CustomListNode {
        const node = $createCustomListNode(serializedNode.listType);
        node.setId(serializedNode.id);
        return node;
    }

    static importDOM(): DOMConversionMap | null {
        // @ts-ignore
        const converter = super.importDOM().ol().conversion as DOMConversionFn<HTMLElement>;
        const customConvertFunction = (element: HTMLElement) => {
            const baseResult = converter(element);
            if (element.id && baseResult?.node) {
                (baseResult.node as CustomListNode).setId(element.id);
            }

            if (baseResult) {
                baseResult.after = $normalizeChildren;
            }

            return baseResult;
        };

        return {
            ol: () => ({
                conversion: customConvertFunction,
                priority: 0,
            }),
            ul: () => ({
                conversion: customConvertFunction,
                priority: 0,
            }),
        };
    }
}

/*
 * This function is a custom normalization function to allow nested lists within list item elements.
 * Original taken from https://github.com/facebook/lexical/blob/6e10210fd1e113ccfafdc999b1d896733c5c5bea/packages/lexical-list/src/LexicalListNode.ts#L284-L303
 * With modifications made.
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 * MIT license
 */
function $normalizeChildren(nodes: Array<LexicalNode>): Array<ListItemNode> {
    const normalizedListItems: Array<ListItemNode> = [];

    for (const node of nodes) {
        if ($isListItemNode(node)) {
            normalizedListItems.push(node);
        } else {
            normalizedListItems.push($wrapInListItem(node));
        }
    }

    return normalizedListItems;
}

function $wrapInListItem(node: LexicalNode): ListItemNode {
    const listItemWrapper = $createCustomListItemNode();
    return listItemWrapper.append(node);
}

export function $createCustomListNode(type: ListType): CustomListNode {
    return new CustomListNode(type, 1);
}

export function $isCustomListNode(node: LexicalNode | null | undefined): node is CustomListNode {
    return node instanceof CustomListNode;
}