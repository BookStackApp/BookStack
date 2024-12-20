/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {ListNode, ListType} from './';
import type {
  BaseSelection,
  DOMConversionMap,
  DOMConversionOutput,
  DOMExportOutput,
  EditorConfig,
  LexicalNode,
  NodeKey,
  ParagraphNode,
  RangeSelection,
  SerializedElementNode,
  Spread,
} from 'lexical';

import {
  $applyNodeReplacement,
  $createParagraphNode,
  $isElementNode,
  $isParagraphNode,
  $isRangeSelection,
  ElementNode,
  LexicalEditor,
} from 'lexical';
import invariant from 'lexical/shared/invariant';

import {$createListNode, $isListNode} from './';
import {mergeLists} from './formatList';
import {isNestedListNode} from './utils';
import {el} from "../../utils/dom";

export type SerializedListItemNode = Spread<
  {
    checked: boolean | undefined;
    value: number;
  },
  SerializedElementNode
>;

/** @noInheritDoc */
export class ListItemNode extends ElementNode {
  /** @internal */
  __value: number;
  /** @internal */
  __checked?: boolean;

  static getType(): string {
    return 'listitem';
  }

  static clone(node: ListItemNode): ListItemNode {
    return new ListItemNode(node.__value, node.__checked, node.__key);
  }

  constructor(value?: number, checked?: boolean, key?: NodeKey) {
    super(key);
    this.__value = value === undefined ? 1 : value;
    this.__checked = checked;
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

    dom.style.listStyle = $hasNestedListWithoutLabel(this) ? 'none' : '';
    // @ts-expect-error - this is always HTMLListItemElement
    dom.value = this.__value;

    return false;
  }

  static transform(): (node: LexicalNode) => void {
    return (node: LexicalNode) => {
      invariant($isListItemNode(node), 'node is not a ListItemNode');
      if (node.__checked == null) {
        return;
      }
      const parent = node.getParent();
      if ($isListNode(parent)) {
        if (parent.getListType() !== 'check' && node.getChecked() != null) {
          node.setChecked(undefined);
        }
      }
    };
  }

  static importDOM(): DOMConversionMap | null {
    return {
      li: () => ({
        conversion: $convertListItemElement,
        priority: 0,
      }),
    };
  }

  static importJSON(serializedNode: SerializedListItemNode): ListItemNode {
    const node = $createListItemNode();
    node.setChecked(serializedNode.checked);
    node.setValue(serializedNode.value);
    node.setDirection(serializedNode.direction);
    return node;
  }

  exportDOM(editor: LexicalEditor): DOMExportOutput {
    const element = this.createDOM(editor._config);

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
      checked: this.getChecked(),
      type: 'listitem',
      value: this.getValue(),
      version: 1,
    };
  }

  append(...nodes: LexicalNode[]): this {
    for (let i = 0; i < nodes.length; i++) {
      const node = nodes[i];

      if ($isElementNode(node) && this.canMergeWith(node)) {
        const children = node.getChildren();
        this.append(...children);
        node.remove();
      } else {
        super.append(node);
      }
    }

    return this;
  }

  replace<N extends LexicalNode>(
    replaceWithNode: N,
    includeChildren?: boolean,
  ): N {
    if ($isListItemNode(replaceWithNode)) {
      return super.replace(replaceWithNode);
    }
    const list = this.getParentOrThrow();
    if (!$isListNode(list)) {
      return replaceWithNode;
    }
    if (list.__first === this.getKey()) {
      list.insertBefore(replaceWithNode);
    } else if (list.__last === this.getKey()) {
      list.insertAfter(replaceWithNode);
    } else {
      // Split the list
      const newList = $createListNode(list.getListType());
      let nextSibling = this.getNextSibling();
      while (nextSibling) {
        const nodeToAppend = nextSibling;
        nextSibling = nextSibling.getNextSibling();
        newList.append(nodeToAppend);
      }
      list.insertAfter(replaceWithNode);
      replaceWithNode.insertAfter(newList);
    }
    if (includeChildren) {
      invariant(
        $isElementNode(replaceWithNode),
        'includeChildren should only be true for ElementNodes',
      );
      this.getChildren().forEach((child: LexicalNode) => {
        replaceWithNode.append(child);
      });
    }
    this.remove();
    if (list.getChildrenSize() === 0) {
      list.remove();
    }
    return replaceWithNode;
  }

  insertAfter(node: LexicalNode, restoreSelection = true): LexicalNode {
    const listNode = this.getParentOrThrow();

    if (!$isListNode(listNode)) {
      invariant(
        false,
        'insertAfter: list node is not parent of list item node',
      );
    }

    if ($isListItemNode(node)) {
      return super.insertAfter(node, restoreSelection);
    }

    const siblings = this.getNextSiblings();

    // Split the lists and insert the node in between them
    listNode.insertAfter(node, restoreSelection);

    if (siblings.length !== 0) {
      const newListNode = $createListNode(listNode.getListType());

      siblings.forEach((sibling) => newListNode.append(sibling));

      node.insertAfter(newListNode, restoreSelection);
    }

    return node;
  }

  remove(preserveEmptyParent?: boolean): void {
    const prevSibling = this.getPreviousSibling();
    const nextSibling = this.getNextSibling();
    super.remove(preserveEmptyParent);

    if (
      prevSibling &&
      nextSibling &&
      isNestedListNode(prevSibling) &&
      isNestedListNode(nextSibling)
    ) {
      mergeLists(prevSibling.getFirstChild(), nextSibling.getFirstChild());
      nextSibling.remove();
    }
  }

  insertNewAfter(
    _: RangeSelection,
    restoreSelection = true,
  ): ListItemNode | ParagraphNode | null {

    if (this.getTextContent().trim() === '' && this.isLastChild()) {
      const list = this.getParentOrThrow<ListNode>();
      const parentListItem = list.getParent();
      if ($isListItemNode(parentListItem)) {
        // Un-nest list item if empty nested item
        parentListItem.insertAfter(this);
        this.selectStart();
        return null;
      } else {
        // Insert empty paragraph after list if adding after last empty child
        const paragraph = $createParagraphNode();
        list.insertAfter(paragraph, restoreSelection);
        this.remove();
        return paragraph;
      }
    }

    const newElement = $createListItemNode(
      this.__checked == null ? undefined : false,
    );

    this.insertAfter(newElement, restoreSelection);

    return newElement;
  }

  collapseAtStart(selection: RangeSelection): true {
    const paragraph = $createParagraphNode();
    const children = this.getChildren();
    children.forEach((child) => paragraph.append(child));
    const listNode = this.getParentOrThrow();
    const listNodeParent = listNode.getParentOrThrow();
    const isIndented = $isListItemNode(listNodeParent);

    if (listNode.getChildrenSize() === 1) {
      if (isIndented) {
        // if the list node is nested, we just want to remove it,
        // effectively unindenting it.
        listNode.remove();
        listNodeParent.select();
      } else {
        listNode.insertBefore(paragraph);
        listNode.remove();
        // If we have selection on the list item, we'll need to move it
        // to the paragraph
        const anchor = selection.anchor;
        const focus = selection.focus;
        const key = paragraph.getKey();

        if (anchor.type === 'element' && anchor.getNode().is(this)) {
          anchor.set(key, anchor.offset, 'element');
        }

        if (focus.type === 'element' && focus.getNode().is(this)) {
          focus.set(key, focus.offset, 'element');
        }
      }
    } else {
      listNode.insertBefore(paragraph);
      this.remove();
    }

    return true;
  }

  getValue(): number {
    const self = this.getLatest();

    return self.__value;
  }

  setValue(value: number): void {
    const self = this.getWritable();
    self.__value = value;
  }

  getChecked(): boolean | undefined {
    const self = this.getLatest();

    let listType: ListType | undefined;

    const parent = this.getParent();
    if ($isListNode(parent)) {
      listType = parent.getListType();
    }

    return listType === 'check' ? Boolean(self.__checked) : undefined;
  }

  setChecked(checked?: boolean): void {
    const self = this.getWritable();
    self.__checked = checked;
  }

  toggleChecked(): void {
    this.setChecked(!this.__checked);
  }

  /** @deprecated @internal */
  canInsertAfter(node: LexicalNode): boolean {
    return $isListItemNode(node);
  }

  /** @deprecated @internal */
  canReplaceWith(replacement: LexicalNode): boolean {
    return $isListItemNode(replacement);
  }

  canMergeWith(node: LexicalNode): boolean {
    return $isParagraphNode(node) || $isListItemNode(node);
  }

  extractWithChild(child: LexicalNode, selection: BaseSelection): boolean {
    if (!$isRangeSelection(selection)) {
      return false;
    }

    const anchorNode = selection.anchor.getNode();
    const focusNode = selection.focus.getNode();

    return (
      this.isParentOf(anchorNode) &&
      this.isParentOf(focusNode) &&
      this.getTextContent().length === selection.getTextContent().length
    );
  }

  isParentRequired(): true {
    return true;
  }

  createParentElementNode(): ElementNode {
    return $createListNode('bullet');
  }

  canMergeWhenEmpty(): true {
    return true;
  }
}

function $hasNestedListWithoutLabel(node: ListItemNode): boolean {
  const children = node.getChildren();
  let hasLabel = false;
  let hasNestedList = false;

  for (const child of children) {
    if ($isListNode(child)) {
      hasNestedList = true;
    } else if (child.getTextContent().trim().length > 0) {
      hasLabel = true;
    }
  }

  return hasNestedList && !hasLabel;
}

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

function $convertListItemElement(domNode: HTMLElement): DOMConversionOutput {
  const isGitHubCheckList = domNode.classList.contains('task-list-item');
  if (isGitHubCheckList) {
    for (const child of domNode.children) {
      if (child.tagName === 'INPUT') {
        return $convertCheckboxInput(child);
      }
    }
  }

  const ariaCheckedAttr = domNode.getAttribute('aria-checked');
  const checked =
    ariaCheckedAttr === 'true'
      ? true
      : ariaCheckedAttr === 'false'
      ? false
      : undefined;
  return {node: $createListItemNode(checked)};
}

function $convertCheckboxInput(domNode: Element): DOMConversionOutput {
  const isCheckboxInput = domNode.getAttribute('type') === 'checkbox';
  if (!isCheckboxInput) {
    return {node: null};
  }
  const checked = domNode.hasAttribute('checked');
  return {node: $createListItemNode(checked)};
}

/**
 * Creates a new List Item node, passing true/false will convert it to a checkbox input.
 * @param checked - Is the List Item a checkbox and, if so, is it checked? undefined/null: not a checkbox, true/false is a checkbox and checked/unchecked, respectively.
 * @returns The new List Item.
 */
export function $createListItemNode(checked?: boolean): ListItemNode {
  return $applyNodeReplacement(new ListItemNode(undefined, checked));
}

/**
 * Checks to see if the node is a ListItemNode.
 * @param node - The node to be checked.
 * @returns true if the node is a ListItemNode, false otherwise.
 */
export function $isListItemNode(
  node: LexicalNode | null | undefined,
): node is ListItemNode {
  return node instanceof ListItemNode;
}
