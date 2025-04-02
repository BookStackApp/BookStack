/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import {
  addClassNamesToElement,
  isHTMLElement,
  removeClassNamesFromElement,
} from '@lexical/utils';
import {
  $applyNodeReplacement,
  $createTextNode,
  $isElementNode,
  DOMConversionMap,
  DOMConversionOutput,
  DOMExportOutput,
  EditorConfig,
  EditorThemeClasses,
  ElementNode,
  LexicalEditor,
  LexicalNode,
  NodeKey,
  SerializedElementNode,
  Spread,
} from 'lexical';
import invariant from 'lexical/shared/invariant';
import normalizeClassNames from 'lexical/shared/normalizeClassNames';

import {$createListItemNode, $isListItemNode, ListItemNode} from '.';
import {
  mergeNextSiblingListIfSameType,
  updateChildrenListItemValue,
} from './formatList';
import {$getListDepth, $wrapInListItem} from './utils';
import {extractDirectionFromElement} from "lexical/nodes/common";

export type SerializedListNode = Spread<
  {
    id: string;
    listType: ListType;
    start: number;
    tag: ListNodeTagType;
  },
  SerializedElementNode
>;

export type ListType = 'number' | 'bullet' | 'check';

export type ListNodeTagType = 'ul' | 'ol';

/** @noInheritDoc */
export class ListNode extends ElementNode {
  /** @internal */
  __tag: ListNodeTagType;
  /** @internal */
  __start: number;
  /** @internal */
  __listType: ListType;
  /** @internal */
  __id: string = '';

  static getType(): string {
    return 'list';
  }

  static clone(node: ListNode): ListNode {
    const newNode = new ListNode(node.__listType, node.__start, node.__key);
    newNode.__id = node.__id;
    newNode.__dir = node.__dir;
    return newNode;
  }

  constructor(listType: ListType, start: number, key?: NodeKey) {
    super(key);
    const _listType = TAG_TO_LIST_TYPE[listType] || listType;
    this.__listType = _listType;
    this.__tag = _listType === 'number' ? 'ol' : 'ul';
    this.__start = start;
  }

  getTag(): ListNodeTagType {
    return this.__tag;
  }

  setId(id: string) {
    const self = this.getWritable();
    self.__id = id;
  }

  getId(): string {
    const self = this.getLatest();
    return self.__id;
  }

  setListType(type: ListType): void {
    const writable = this.getWritable();
    writable.__listType = type;
    writable.__tag = type === 'number' ? 'ol' : 'ul';
  }

  getListType(): ListType {
    return this.__listType;
  }

  getStart(): number {
    return this.__start;
  }

  // View

  createDOM(config: EditorConfig, _editor?: LexicalEditor): HTMLElement {
    const tag = this.__tag;
    const dom = document.createElement(tag);

    if (this.__start !== 1) {
      dom.setAttribute('start', String(this.__start));
    }
    // @ts-expect-error Internal field.
    dom.__lexicalListType = this.__listType;
    $setListThemeClassNames(dom, config.theme, this);

    if (this.__id) {
      dom.setAttribute('id', this.__id);
    }

    if (this.__dir) {
      dom.setAttribute('dir', this.__dir);
    }

    return dom;
  }

  updateDOM(
    prevNode: ListNode,
    dom: HTMLElement,
    config: EditorConfig,
  ): boolean {
    if (
        prevNode.__tag !== this.__tag
        || prevNode.__dir !== this.__dir
        || prevNode.__id !== this.__id
    ) {
      return true;
    }

    $setListThemeClassNames(dom, config.theme, this);

    return false;
  }

  static transform(): (node: LexicalNode) => void {
    return (node: LexicalNode) => {
      invariant($isListNode(node), 'node is not a ListNode');
      mergeNextSiblingListIfSameType(node);
      updateChildrenListItemValue(node);
    };
  }

  static importDOM(): DOMConversionMap | null {
    return {
      ol: () => ({
        conversion: $convertListNode,
        priority: 0,
      }),
      ul: () => ({
        conversion: $convertListNode,
        priority: 0,
      }),
    };
  }

  static importJSON(serializedNode: SerializedListNode): ListNode {
    const node = $createListNode(serializedNode.listType, serializedNode.start);
    node.setId(serializedNode.id);
    node.setDirection(serializedNode.direction);
    return node;
  }

  exportDOM(editor: LexicalEditor): DOMExportOutput {
    const {element} = super.exportDOM(editor);
    if (element && isHTMLElement(element)) {
      if (this.__start !== 1) {
        element.setAttribute('start', String(this.__start));
      }
      if (this.__listType === 'check') {
        element.setAttribute('__lexicalListType', 'check');
      }
    }
    return {
      element,
    };
  }

  exportJSON(): SerializedListNode {
    return {
      ...super.exportJSON(),
      listType: this.getListType(),
      start: this.getStart(),
      tag: this.getTag(),
      type: 'list',
      version: 1,
      id: this.__id,
    };
  }

  canBeEmpty(): false {
    return false;
  }

  canIndent(): false {
    return false;
  }

  append(...nodesToAppend: LexicalNode[]): this {
    for (let i = 0; i < nodesToAppend.length; i++) {
      const currentNode = nodesToAppend[i];

      if ($isListItemNode(currentNode)) {
        super.append(currentNode);
      } else {
        const listItemNode = $createListItemNode();

        if ($isListNode(currentNode)) {
          listItemNode.append(currentNode);
        } else if ($isElementNode(currentNode)) {
          const textNode = $createTextNode(currentNode.getTextContent());
          listItemNode.append(textNode);
        } else {
          listItemNode.append(currentNode);
        }
        super.append(listItemNode);
      }
    }
    return this;
  }

  extractWithChild(child: LexicalNode): boolean {
    return $isListItemNode(child);
  }
}

function $setListThemeClassNames(
  dom: HTMLElement,
  editorThemeClasses: EditorThemeClasses,
  node: ListNode,
): void {
  const classesToAdd = [];
  const classesToRemove = [];
  const listTheme = editorThemeClasses.list;

  if (listTheme !== undefined) {
    const listLevelsClassNames = listTheme[`${node.__tag}Depth`] || [];
    const listDepth = $getListDepth(node) - 1;
    const normalizedListDepth = listDepth % listLevelsClassNames.length;
    const listLevelClassName = listLevelsClassNames[normalizedListDepth];
    const listClassName = listTheme[node.__tag];
    let nestedListClassName;
    const nestedListTheme = listTheme.nested;
    const checklistClassName = listTheme.checklist;

    if (nestedListTheme !== undefined && nestedListTheme.list) {
      nestedListClassName = nestedListTheme.list;
    }

    if (listClassName !== undefined) {
      classesToAdd.push(listClassName);
    }

    if (checklistClassName !== undefined && node.__listType === 'check') {
      classesToAdd.push(checklistClassName);
    }

    if (listLevelClassName !== undefined) {
      classesToAdd.push(...normalizeClassNames(listLevelClassName));
      for (let i = 0; i < listLevelsClassNames.length; i++) {
        if (i !== normalizedListDepth) {
          classesToRemove.push(node.__tag + i);
        }
      }
    }

    if (nestedListClassName !== undefined) {
      const nestedListItemClasses = normalizeClassNames(nestedListClassName);

      if (listDepth > 1) {
        classesToAdd.push(...nestedListItemClasses);
      } else {
        classesToRemove.push(...nestedListItemClasses);
      }
    }
  }

  if (classesToRemove.length > 0) {
    removeClassNamesFromElement(dom, ...classesToRemove);
  }

  if (classesToAdd.length > 0) {
    addClassNamesToElement(dom, ...classesToAdd);
  }
}

/*
 * This function is a custom normalization function to allow nested lists within list item elements.
 * Original taken from https://github.com/facebook/lexical/blob/6e10210fd1e113ccfafdc999b1d896733c5c5bea/packages/lexical-list/src/LexicalListNode.ts#L284-L303
 * With modifications made.
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

function isDomChecklist(domNode: HTMLElement) {
  if (
    domNode.getAttribute('__lexicallisttype') === 'check' ||
    // is github checklist
    domNode.classList.contains('contains-task-list')
  ) {
    return true;
  }
  // if children are checklist items, the node is a checklist ul. Applicable for googledoc checklist pasting.
  for (const child of domNode.childNodes) {
    if (!isHTMLElement(child)) {
      continue;
    }

    if (child.hasAttribute('aria-checked')) {
      return true;
    }

    if (child.classList.contains('task-list-item')) {
      return true;
    }

    if (child.firstElementChild && child.firstElementChild.matches('input[type="checkbox"]')) {
      return true;
    }
  }
  return false;
}

function $convertListNode(domNode: HTMLElement): DOMConversionOutput {
  const nodeName = domNode.nodeName.toLowerCase();
  let node = null;
  if (nodeName === 'ol') {
    // @ts-ignore
    const start = domNode.start;
    node = $createListNode('number', start);
  } else if (nodeName === 'ul') {
    if (isDomChecklist(domNode)) {
      node = $createListNode('check');
    } else {
      node = $createListNode('bullet');
    }
  }

  if (domNode.id && node) {
    node.setId(domNode.id);
  }

  if (domNode.dir && node) {
    node.setDirection(extractDirectionFromElement(domNode));
  }

  return {
    after: $normalizeChildren,
    node,
  };
}

const TAG_TO_LIST_TYPE: Record<string, ListType> = {
  ol: 'number',
  ul: 'bullet',
};

/**
 * Creates a ListNode of listType.
 * @param listType - The type of list to be created. Can be 'number', 'bullet', or 'check'.
 * @param start - Where an ordered list starts its count, start = 1 if left undefined.
 * @returns The new ListNode
 */
export function $createListNode(listType: ListType, start = 1): ListNode {
  return $applyNodeReplacement(new ListNode(listType, start));
}

/**
 * Checks to see if the node is a ListNode.
 * @param node - The node to be checked.
 * @returns true if the node is a ListNode, false otherwise.
 */
export function $isListNode(
  node: LexicalNode | null | undefined,
): node is ListNode {
  return node instanceof ListNode;
}
