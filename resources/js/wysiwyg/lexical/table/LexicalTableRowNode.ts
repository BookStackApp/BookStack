/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {Spread} from 'lexical';

import {addClassNamesToElement} from '@lexical/utils';
import {
  $applyNodeReplacement,
  DOMConversionMap,
  DOMConversionOutput,
  EditorConfig,
  ElementNode,
  LexicalNode,
  NodeKey,
  SerializedElementNode,
} from 'lexical';

import {extractStyleMapFromElement, sizeToPixels, StyleMap} from "../../utils/dom";

export type SerializedTableRowNode = Spread<
  {
    styles: Record<string, string>,
    height?: number,
  },
  SerializedElementNode
>;

/** @noInheritDoc */
export class TableRowNode extends ElementNode {
  /** @internal */
  __height?: number;
  /** @internal */
  __styles: StyleMap = new Map();

  static getType(): string {
    return 'tablerow';
  }

  static clone(node: TableRowNode): TableRowNode {
    const newNode = new TableRowNode(node.__key);
    newNode.__styles = new Map(node.__styles);
    return newNode;
  }

  static importDOM(): DOMConversionMap | null {
    return {
      tr: (node: Node) => ({
        conversion: $convertTableRowElement,
        priority: 0,
      }),
    };
  }

  static importJSON(serializedNode: SerializedTableRowNode): TableRowNode {
    const node = $createTableRowNode();

    node.setStyles(new Map(Object.entries(serializedNode.styles)));

    return node;
  }

  constructor(key?: NodeKey) {
    super(key);
  }

  exportJSON(): SerializedTableRowNode {
    return {
      ...super.exportJSON(),
      type: 'tablerow',
      version: 1,
      styles: Object.fromEntries(this.__styles),
      height: this.__height || 0,
    };
  }

  createDOM(config: EditorConfig): HTMLElement {
    const element = document.createElement('tr');

    if (this.__height) {
      element.style.height = `${this.__height}px`;
    }

    for (const [name, value] of this.__styles.entries()) {
      element.style.setProperty(name, value);
    }

    addClassNamesToElement(element, config.theme.tableRow);

    return element;
  }

  isShadowRoot(): boolean {
    return true;
  }

  getStyles(): StyleMap {
    const self = this.getLatest();
    return new Map(self.__styles);
  }

  setStyles(styles: StyleMap): void {
    const self = this.getWritable();
    self.__styles = new Map(styles);
  }

  setHeight(height: number): number | null | undefined {
    const self = this.getWritable();
    self.__height = height;
    return this.__height;
  }

  getHeight(): number | undefined {
    return this.getLatest().__height;
  }

  updateDOM(prevNode: TableRowNode): boolean {
    return prevNode.__height !== this.__height
        || prevNode.__styles !== this.__styles;
  }

  canBeEmpty(): false {
    return false;
  }

  canIndent(): false {
    return false;
  }
}

export function $convertTableRowElement(domNode: Node): DOMConversionOutput {
  const rowNode = $createTableRowNode();
  const domNode_ = domNode as HTMLElement;

  const height = sizeToPixels(domNode_.style.height);
  rowNode.setHeight(height);

  if (domNode instanceof HTMLElement) {
    rowNode.setStyles(extractStyleMapFromElement(domNode));
  }

  return {node: rowNode};
}

export function $createTableRowNode(): TableRowNode {
  return $applyNodeReplacement(new TableRowNode());
}

export function $isTableRowNode(
  node: LexicalNode | null | undefined,
): node is TableRowNode {
  return node instanceof TableRowNode;
}
