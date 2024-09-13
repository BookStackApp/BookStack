import {$isListNode, ListItemNode, SerializedListItemNode} from "@lexical/list";
import {EditorConfig} from "lexical/LexicalEditor";
import {DOMExportOutput, LexicalEditor, LexicalNode} from "lexical";

import {el} from "../utils/dom";
import {$isCustomListNode} from "./custom-list";

function updateListItemChecked(
    dom: HTMLElement,
    listItemNode: ListItemNode,
): void {
    // Only set task list attrs for leaf list items
    const shouldBeTaskItem = !$isListNode(listItemNode.getFirstChild());
    dom.classList.toggle('task-list-item', shouldBeTaskItem);
    if (listItemNode.__checked) {
        dom.setAttribute('checked', 'checked');
    } else {
        dom.removeAttribute('checked');
    }
}


export class CustomListItemNode extends ListItemNode {
    static getType(): string {
        return 'custom-list-item';
    }

    static clone(node: CustomListItemNode): CustomListItemNode {
        return new CustomListItemNode(node.__value, node.__checked, node.__key);
    }

    createDOM(config: EditorConfig): HTMLElement {
        const element = document.createElement('li');
        const parent = this.getParent();

        if ($isListNode(parent) && parent.getListType() === 'check') {
            updateListItemChecked(element, this);
        }

        element.value = this.__value;

        if ($hasNestedListWithoutLabel(this)) {
            element.style.listStyle = 'none';
        }

        return element;
    }

    updateDOM(
        prevNode: ListItemNode,
        dom: HTMLElement,
        config: EditorConfig,
    ): boolean {
        const parent = this.getParent();
        if ($isListNode(parent) && parent.getListType() === 'check') {
            updateListItemChecked(dom, this);
        }
        // @ts-expect-error - this is always HTMLListItemElement
        dom.value = this.__value;

        return false;
    }

    exportDOM(editor: LexicalEditor): DOMExportOutput {
        const element = this.createDOM(editor._config);
        element.style.textAlign = this.getFormatType();

        if (element.classList.contains('task-list-item')) {
            const input = el('input', {
                type: 'checkbox',
                disabled: 'disabled',
            });
            if (element.hasAttribute('checked')) {
                input.setAttribute('checked', 'checked');
                element.removeAttribute('checked');
            }

            element.prepend(input);
        }

        return {
            element,
        };
    }

    exportJSON(): SerializedListItemNode {
        return {
            ...super.exportJSON(),
            type: 'custom-list-item',
        };
    }
}

function $hasNestedListWithoutLabel(node: CustomListItemNode): boolean {
    const children = node.getChildren();
    let hasLabel = false;
    let hasNestedList = false;

    for (const child of children) {
        if ($isCustomListNode(child)) {
            hasNestedList = true;
        } else if (child.getTextContent().trim().length > 0) {
            hasLabel = true;
        }
    }

    return hasNestedList && !hasLabel;
}

export function $isCustomListItemNode(
    node: LexicalNode | null | undefined,
): node is CustomListItemNode {
    return node instanceof CustomListItemNode;
}

export function $createCustomListItemNode(): CustomListItemNode {
    return new CustomListItemNode();
}