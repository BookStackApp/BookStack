/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {
  DOMConversionMap,
  DOMConversionOutput,
  DOMExportOutput,
  EditorConfig,
  LexicalEditor,
  LexicalNode,
  NodeKey,
  SerializedElementNode,
  Spread,
} from 'lexical';

import {addClassNamesToElement} from '@lexical/utils';
import {
  $applyNodeReplacement,
  $createParagraphNode,
  $isElementNode,
  $isLineBreakNode,
  $isTextNode,
  ElementNode,
} from 'lexical';

import {extractStyleMapFromElement, StyleMap} from "../../utils/dom";
import {CommonBlockAlignment, extractAlignmentFromElement} from "lexical/nodes/common";

export const TableCellHeaderStates = {
  BOTH: 3,
  COLUMN: 2,
  NO_STATUS: 0,
  ROW: 1,
};

export type TableCellHeaderState =
  typeof TableCellHeaderStates[keyof typeof TableCellHeaderStates];

export type SerializedTableCellNode = Spread<
  {
    colSpan?: number;
    rowSpan?: number;
    headerState: TableCellHeaderState;
    width?: number;
    backgroundColor?: null | string;
    styles: Record<string, string>;
    alignment: CommonBlockAlignment;
  },
  SerializedElementNode
>;

/** @noInheritDoc */
export class TableCellNode extends ElementNode {
  /** @internal */
  __colSpan: number;
  /** @internal */
  __rowSpan: number;
  /** @internal */
  __headerState: TableCellHeaderState;
  /** @internal */
  __width?: number;
  /** @internal */
  __backgroundColor: null | string;
  /** @internal */
  __styles: StyleMap = new Map;
  /** @internal */
  __alignment: CommonBlockAlignment = '';

  static getType(): string {
    return 'tablecell';
  }

  static clone(node: TableCellNode): TableCellNode {
    const cellNode = new TableCellNode(
      node.__headerState,
      node.__colSpan,
      node.__width,
      node.__key,
    );
    cellNode.__rowSpan = node.__rowSpan;
    cellNode.__backgroundColor = node.__backgroundColor;
    cellNode.__styles = new Map(node.__styles);
    cellNode.__alignment = node.__alignment;
    return cellNode;
  }

  static importDOM(): DOMConversionMap | null {
    return {
      td: (node: Node) => ({
        conversion: $convertTableCellNodeElement,
        priority: 0,
      }),
      th: (node: Node) => ({
        conversion: $convertTableCellNodeElement,
        priority: 0,
      }),
    };
  }

  static importJSON(serializedNode: SerializedTableCellNode): TableCellNode {
    const node = $createTableCellNode(
        serializedNode.headerState,
        serializedNode.colSpan,
        serializedNode.width,
    );

    if (serializedNode.rowSpan) {
        node.setRowSpan(serializedNode.rowSpan);
    }

    node.setStyles(new Map(Object.entries(serializedNode.styles)));
    node.setAlignment(serializedNode.alignment);

    return node;
  }

  constructor(
    headerState = TableCellHeaderStates.NO_STATUS,
    colSpan = 1,
    width?: number,
    key?: NodeKey,
  ) {
    super(key);
    this.__colSpan = colSpan;
    this.__rowSpan = 1;
    this.__headerState = headerState;
    this.__width = width;
    this.__backgroundColor = null;
  }

  createDOM(config: EditorConfig): HTMLElement {
    const element = document.createElement(
      this.getTag(),
    ) as HTMLTableCellElement;

    if (this.__width) {
      element.style.width = `${this.__width}px`;
    }
    if (this.__colSpan > 1) {
      element.colSpan = this.__colSpan;
    }
    if (this.__rowSpan > 1) {
      element.rowSpan = this.__rowSpan;
    }
    if (this.__backgroundColor !== null) {
      element.style.backgroundColor = this.__backgroundColor;
    }

    addClassNamesToElement(
      element,
      config.theme.tableCell,
      this.hasHeader() && config.theme.tableCellHeader,
    );

    for (const [name, value] of this.__styles.entries()) {
      element.style.setProperty(name, value);
    }

    if (this.__alignment) {
      element.classList.add('align-' + this.__alignment);
    }

    return element;
  }

  exportDOM(editor: LexicalEditor): DOMExportOutput {
    const {element} = super.exportDOM(editor);
    return {
      element,
    };
  }

  exportJSON(): SerializedTableCellNode {
    return {
      ...super.exportJSON(),
      backgroundColor: this.getBackgroundColor(),
      colSpan: this.__colSpan,
      headerState: this.__headerState,
      rowSpan: this.__rowSpan,
      type: 'tablecell',
      width: this.getWidth(),
      styles: Object.fromEntries(this.__styles),
      alignment: this.__alignment,
    };
  }

  getColSpan(): number {
    return this.__colSpan;
  }

  setColSpan(colSpan: number): this {
    this.getWritable().__colSpan = colSpan;
    return this;
  }

  getRowSpan(): number {
    return this.__rowSpan;
  }

  setRowSpan(rowSpan: number): this {
    this.getWritable().__rowSpan = rowSpan;
    return this;
  }

  getTag(): string {
    return this.hasHeader() ? 'th' : 'td';
  }

  setHeaderStyles(headerState: TableCellHeaderState): TableCellHeaderState {
    const self = this.getWritable();
    self.__headerState = headerState;
    return this.__headerState;
  }

  getHeaderStyles(): TableCellHeaderState {
    return this.getLatest().__headerState;
  }

  setWidth(width: number): number | null | undefined {
    const self = this.getWritable();
    self.__width = width;
    return this.__width;
  }

  getWidth(): number | undefined {
    return this.getLatest().__width;
  }

  clearWidth(): void {
    const self = this.getWritable();
    self.__width = undefined;
  }

  getStyles(): StyleMap {
    const self = this.getLatest();
    return new Map(self.__styles);
  }

  setStyles(styles: StyleMap): void {
    const self = this.getWritable();
    self.__styles = new Map(styles);
  }

  setAlignment(alignment: CommonBlockAlignment) {
    const self = this.getWritable();
    self.__alignment = alignment;
  }

  getAlignment(): CommonBlockAlignment {
    const self = this.getLatest();
    return self.__alignment;
  }

  updateTag(tag: string): void {
    const isHeader = tag.toLowerCase() === 'th';
    const state = isHeader ? TableCellHeaderStates.ROW : TableCellHeaderStates.NO_STATUS;
    const self = this.getWritable();
    self.__headerState = state;
  }

  getBackgroundColor(): null | string {
    return this.getLatest().__backgroundColor;
  }

  setBackgroundColor(newBackgroundColor: null | string): void {
    this.getWritable().__backgroundColor = newBackgroundColor;
  }

  toggleHeaderStyle(headerStateToToggle: TableCellHeaderState): TableCellNode {
    const self = this.getWritable();

    if ((self.__headerState & headerStateToToggle) === headerStateToToggle) {
      self.__headerState -= headerStateToToggle;
    } else {
      self.__headerState += headerStateToToggle;
    }

    return self;
  }

  hasHeaderState(headerState: TableCellHeaderState): boolean {
    return (this.getHeaderStyles() & headerState) === headerState;
  }

  hasHeader(): boolean {
    return this.getLatest().__headerState !== TableCellHeaderStates.NO_STATUS;
  }

  updateDOM(prevNode: TableCellNode): boolean {
    return (
      prevNode.__headerState !== this.__headerState ||
      prevNode.__width !== this.__width ||
      prevNode.__colSpan !== this.__colSpan ||
      prevNode.__rowSpan !== this.__rowSpan ||
      prevNode.__backgroundColor !== this.__backgroundColor ||
      prevNode.__styles !== this.__styles ||
      prevNode.__alignment !== this.__alignment
    );
  }

  isShadowRoot(): boolean {
    return true;
  }

  collapseAtStart(): true {
    return true;
  }

  canBeEmpty(): false {
    return false;
  }

  canIndent(): false {
    return false;
  }
}

export function $convertTableCellNodeElement(
    domNode: Node,
): DOMConversionOutput {
  const domNode_ = domNode as HTMLTableCellElement;
  const nodeName = domNode.nodeName.toLowerCase();

  let width: number | undefined = undefined;


  const PIXEL_VALUE_REG_EXP = /^(\d+(?:\.\d+)?)px$/;
  if (PIXEL_VALUE_REG_EXP.test(domNode_.style.width)) {
    width = parseFloat(domNode_.style.width);
  }

  const tableCellNode = $createTableCellNode(
      nodeName === 'th'
          ? TableCellHeaderStates.ROW
          : TableCellHeaderStates.NO_STATUS,
      domNode_.colSpan,
      width,
  );

  tableCellNode.__rowSpan = domNode_.rowSpan;

  const style = domNode_.style;
  const textDecoration = style.textDecoration.split(' ');
  const hasBoldFontWeight =
      style.fontWeight === '700' || style.fontWeight === 'bold';
  const hasLinethroughTextDecoration = textDecoration.includes('line-through');
  const hasItalicFontStyle = style.fontStyle === 'italic';
  const hasUnderlineTextDecoration = textDecoration.includes('underline');

  if (domNode instanceof HTMLElement) {
    const styleMap = extractStyleMapFromElement(domNode);
    styleMap.delete('background-color');
    tableCellNode.setStyles(styleMap);
    tableCellNode.setAlignment(extractAlignmentFromElement(domNode));
  }

  const background = style.backgroundColor || null;
  if (background) {
    tableCellNode.setBackgroundColor(background);
  }

  return {
    after: (childLexicalNodes) => {
      if (childLexicalNodes.length === 0) {
        childLexicalNodes.push($createParagraphNode());
      }
      return childLexicalNodes;
    },
    forChild: (lexicalNode, parentLexicalNode) => {
      if ($isTableCellNode(parentLexicalNode) && !$isElementNode(lexicalNode)) {
        const paragraphNode = $createParagraphNode();
        if (
            $isLineBreakNode(lexicalNode) &&
            lexicalNode.getTextContent() === '\n'
        ) {
          return null;
        }
        if ($isTextNode(lexicalNode)) {
          if (hasBoldFontWeight) {
            lexicalNode.toggleFormat('bold');
          }
          if (hasLinethroughTextDecoration) {
            lexicalNode.toggleFormat('strikethrough');
          }
          if (hasItalicFontStyle) {
            lexicalNode.toggleFormat('italic');
          }
          if (hasUnderlineTextDecoration) {
            lexicalNode.toggleFormat('underline');
          }
        }
        paragraphNode.append(lexicalNode);
        return paragraphNode;
      }

      return lexicalNode;
    },
    node: tableCellNode,
  };
}

export function $createTableCellNode(
  headerState: TableCellHeaderState = TableCellHeaderStates.NO_STATUS,
  colSpan = 1,
  width?: number,
): TableCellNode {
  return $applyNodeReplacement(new TableCellNode(headerState, colSpan, width));
}

export function $isTableCellNode(
  node: LexicalNode | null | undefined,
): node is TableCellNode {
  return node instanceof TableCellNode;
}
