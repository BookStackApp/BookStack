/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {TableCellNode} from './LexicalTableCellNode';
import {
  DOMConversionMap,
  DOMConversionOutput,
  DOMExportOutput,
  EditorConfig,
  LexicalEditor,
  LexicalNode,
  NodeKey,
  Spread,
} from 'lexical';

import {addClassNamesToElement, isHTMLElement} from '@lexical/utils';
import {
  $applyNodeReplacement,
  $getNearestNodeFromDOMNode,

} from 'lexical';

import {$isTableCellNode} from './LexicalTableCellNode';
import {TableDOMCell, TableDOMTable} from './LexicalTableObserver';
import {getTable} from './LexicalTableSelectionHelpers';
import {CommonBlockNode, copyCommonBlockProperties, SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";
import {
  applyCommonPropertyChanges,
  commonPropertiesDifferent, deserializeCommonBlockNode,
  setCommonBlockPropsFromElement,
  updateElementWithCommonBlockProps
} from "lexical/nodes/common";
import {el, extractStyleMapFromElement, StyleMap} from "../../utils/dom";
import {buildColgroupFromTableWidths, getTableColumnWidths} from "../../utils/tables";

export type SerializedTableNode = Spread<{
  colWidths: string[];
  styles: Record<string, string>,
}, SerializedCommonBlockNode>

/** @noInheritDoc */
export class TableNode extends CommonBlockNode {
  __colWidths: string[] = [];
  __styles: StyleMap = new Map;

  static getType(): string {
    return 'table';
  }

  static clone(node: TableNode): TableNode {
    const newNode = new TableNode(node.__key);
    copyCommonBlockProperties(node, newNode);
    newNode.__colWidths = [...node.__colWidths];
    newNode.__styles = new Map(node.__styles);
    return newNode;
  }

  static importDOM(): DOMConversionMap | null {
    return {
      table: (_node: Node) => ({
        conversion: $convertTableElement,
        priority: 1,
      }),
    };
  }

  static importJSON(_serializedNode: SerializedTableNode): TableNode {
    const node = $createTableNode();
    deserializeCommonBlockNode(_serializedNode, node);
    node.setColWidths(_serializedNode.colWidths);
    node.setStyles(new Map(Object.entries(_serializedNode.styles)));
    return node;
  }

  constructor(key?: NodeKey) {
    super(key);
  }

  exportJSON(): SerializedTableNode {
    return {
      ...super.exportJSON(),
      type: 'table',
      version: 1,
      colWidths: this.__colWidths,
      styles: Object.fromEntries(this.__styles),
    };
  }

  createDOM(config: EditorConfig, editor?: LexicalEditor): HTMLElement {
    const tableElement = document.createElement('table');

    addClassNamesToElement(tableElement, config.theme.table);

    updateElementWithCommonBlockProps(tableElement, this);

    const colWidths = this.getColWidths();
    const colgroup = buildColgroupFromTableWidths(colWidths);
    if (colgroup) {
      tableElement.append(colgroup);
    }

    for (const [name, value] of this.__styles.entries()) {
      tableElement.style.setProperty(name, value);
    }

    return tableElement;
  }

  updateDOM(_prevNode: TableNode, dom: HTMLElement): boolean {
    applyCommonPropertyChanges(_prevNode, this, dom);

    if (this.__colWidths.join(':') !== _prevNode.__colWidths.join(':')) {
      const existingColGroup = Array.from(dom.children).find(child => child.nodeName === 'COLGROUP');
      const newColGroup = buildColgroupFromTableWidths(this.__colWidths);
      if (existingColGroup) {
        existingColGroup.remove();
      }

      if (newColGroup) {
        dom.prepend(newColGroup);
      }
    }

    if (Array.from(this.__styles.values()).join(':') !== Array.from(_prevNode.__styles.values()).join(':')) {
      dom.style.cssText = '';
      for (const [name, value] of this.__styles.entries()) {
        dom.style.setProperty(name, value);
      }
    }

    return false;
  }

  exportDOM(editor: LexicalEditor): DOMExportOutput {
    return {
      ...super.exportDOM(editor),
      after: (tableElement) => {
        if (!tableElement) {
          return;
        }

        const newElement = tableElement.cloneNode() as ParentNode;
        const tBody = document.createElement('tbody');

        if (isHTMLElement(tableElement)) {
          for (const child of Array.from(tableElement.children)) {
            if (child.nodeName === 'TR') {
              tBody.append(child);
            } else if (child.nodeName === 'CAPTION') {
              newElement.insertBefore(child, newElement.firstChild);
            } else {
              newElement.append(child);
            }
          }
        }

        newElement.append(tBody);

        return newElement as HTMLElement;
      },
    };
  }

  canBeEmpty(): false {
    return false;
  }

  isShadowRoot(): boolean {
    return true;
  }

  setColWidths(widths: string[]) {
    const self = this.getWritable();
    self.__colWidths = widths;
  }

  getColWidths(): string[] {
    const self = this.getLatest();
    return [...self.__colWidths];
  }

  getStyles(): StyleMap {
    const self = this.getLatest();
    return new Map(self.__styles);
  }

  setStyles(styles: StyleMap): void {
    const self = this.getWritable();
    self.__styles = new Map(styles);
  }

  getCordsFromCellNode(
    tableCellNode: TableCellNode,
    table: TableDOMTable,
  ): {x: number; y: number} {
    const {rows, domRows} = table;

    for (let y = 0; y < rows; y++) {
      const row = domRows[y];

      if (row == null) {
        continue;
      }

      const x = row.findIndex((cell) => {
        if (!cell) {
          return;
        }
        const {elem} = cell;
        const cellNode = $getNearestNodeFromDOMNode(elem);
        return cellNode === tableCellNode;
      });

      if (x !== -1) {
        return {x, y};
      }
    }

    throw new Error('Cell not found in table.');
  }

  getDOMCellFromCords(
    x: number,
    y: number,
    table: TableDOMTable,
  ): null | TableDOMCell {
    const {domRows} = table;

    const row = domRows[y];

    if (row == null) {
      return null;
    }

    const index = x < row.length ? x : row.length - 1;

    const cell = row[index];

    if (cell == null) {
      return null;
    }

    return cell;
  }

  getDOMCellFromCordsOrThrow(
    x: number,
    y: number,
    table: TableDOMTable,
  ): TableDOMCell {
    const cell = this.getDOMCellFromCords(x, y, table);

    if (!cell) {
      throw new Error('Cell not found at cords.');
    }

    return cell;
  }

  getCellNodeFromCords(
    x: number,
    y: number,
    table: TableDOMTable,
  ): null | TableCellNode {
    const cell = this.getDOMCellFromCords(x, y, table);

    if (cell == null) {
      return null;
    }

    const node = $getNearestNodeFromDOMNode(cell.elem);

    if ($isTableCellNode(node)) {
      return node;
    }

    return null;
  }

  getCellNodeFromCordsOrThrow(
    x: number,
    y: number,
    table: TableDOMTable,
  ): TableCellNode {
    const node = this.getCellNodeFromCords(x, y, table);

    if (!node) {
      throw new Error('Node at cords not TableCellNode.');
    }

    return node;
  }

  canSelectBefore(): true {
    return true;
  }

  canIndent(): false {
    return false;
  }
}

export function $getElementForTableNode(
  editor: LexicalEditor,
  tableNode: TableNode,
): TableDOMTable {
  const tableElement = editor.getElementByKey(tableNode.getKey());

  if (tableElement == null) {
    throw new Error('Table Element Not Found');
  }

  return getTable(tableElement);
}

export function $convertTableElement(element: HTMLElement): DOMConversionOutput {
  const node = $createTableNode();
  setCommonBlockPropsFromElement(element, node);

  const colWidths = getTableColumnWidths(element as HTMLTableElement);
  node.setColWidths(colWidths);
  node.setStyles(extractStyleMapFromElement(element));

  return {node};
}

export function $createTableNode(): TableNode {
  return $applyNodeReplacement(new TableNode());
}

export function $isTableNode(
  node: LexicalNode | null | undefined,
): node is TableNode {
  return node instanceof TableNode;
}
