import {
    DOMConversionFn,
    DOMConversionMap,
    LexicalNode,
    Spread
} from "lexical";
import {EditorConfig} from "lexical/LexicalEditor";
import {ListNode, ListType, SerializedListNode} from "@lexical/list";


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
        const newNode = new CustomListNode(node.__listType, 0, node.__key);
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

export function $createCustomListNode(type: ListType): CustomListNode {
    return new CustomListNode(type, 0);
}

export function $isCustomListNode(node: LexicalNode | null | undefined): node is CustomListNode {
    return node instanceof CustomListNode;
}