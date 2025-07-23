/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {
  EditorConfig,
  KlassConstructor,
  LexicalEditor,
  Spread,
} from '../LexicalEditor';
import type {
  DOMConversionMap,
  DOMConversionOutput,
  DOMExportOutput,
  LexicalNode,
  NodeKey,
} from '../LexicalNode';
import {RangeSelection, TEXT_TYPE_TO_FORMAT, TextFormatType} from 'lexical';

import {
  $applyNodeReplacement,
  getCachedClassNameArray,
  isHTMLElement,
} from '../LexicalUtils';
import {$isTextNode} from './LexicalTextNode';
import {
  commonPropertiesDifferent, deserializeCommonBlockNode,
  setCommonBlockPropsFromElement,
  updateElementWithCommonBlockProps
} from "./common";
import {CommonBlockNode, copyCommonBlockProperties, SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";

export type SerializedParagraphNode = Spread<
  {
    textFormat: number;
    textStyle: string;
  },
  SerializedCommonBlockNode
>;

/** @noInheritDoc */
export class ParagraphNode extends CommonBlockNode {
  declare ['constructor']: KlassConstructor<typeof ParagraphNode>;
  /** @internal */
  __textFormat: number;
  __textStyle: string;

  constructor(key?: NodeKey) {
    super(key);
    this.__textFormat = 0;
    this.__textStyle = '';
  }

  static getType(): string {
    return 'paragraph';
  }

  getTextFormat(): number {
    const self = this.getLatest();
    return self.__textFormat;
  }

  setTextFormat(type: number): this {
    const self = this.getWritable();
    self.__textFormat = type;
    return self;
  }

  hasTextFormat(type: TextFormatType): boolean {
    const formatFlag = TEXT_TYPE_TO_FORMAT[type];
    return (this.getTextFormat() & formatFlag) !== 0;
  }

  getTextStyle(): string {
    const self = this.getLatest();
    return self.__textStyle;
  }

  setTextStyle(style: string): this {
    const self = this.getWritable();
    self.__textStyle = style;
    return self;
  }

  static clone(node: ParagraphNode): ParagraphNode {
    return new ParagraphNode(node.__key);
  }

  afterCloneFrom(prevNode: this) {
    super.afterCloneFrom(prevNode);
    this.__textFormat = prevNode.__textFormat;
    this.__textStyle = prevNode.__textStyle;
    copyCommonBlockProperties(prevNode, this);
  }

  // View

  createDOM(config: EditorConfig): HTMLElement {
    const dom = document.createElement('p');
    const classNames = getCachedClassNameArray(config.theme, 'paragraph');
    if (classNames !== undefined) {
      const domClassList = dom.classList;
      domClassList.add(...classNames);
    }

    updateElementWithCommonBlockProps(dom, this);

    return dom;
  }
  updateDOM(
    prevNode: ParagraphNode,
    dom: HTMLElement,
    config: EditorConfig,
  ): boolean {
    return commonPropertiesDifferent(prevNode, this);
  }

  static importDOM(): DOMConversionMap | null {
    return {
      p: (node: Node) => ({
        conversion: $convertParagraphElement,
        priority: 0,
      }),
    };
  }

  exportDOM(editor: LexicalEditor): DOMExportOutput {
    const {element} = super.exportDOM(editor);

    if (element && isHTMLElement(element)) {
      if (this.isEmpty()) {
        element.append(document.createElement('br'));
      }
    }

    return {
      element,
    };
  }

  static importJSON(serializedNode: SerializedParagraphNode): ParagraphNode {
    const node = $createParagraphNode();
    deserializeCommonBlockNode(serializedNode, node);
    node.setTextFormat(serializedNode.textFormat);
    return node;
  }

  exportJSON(): SerializedParagraphNode {
    return {
      ...super.exportJSON(),
      textFormat: this.getTextFormat(),
      textStyle: this.getTextStyle(),
      type: 'paragraph',
      version: 1,
    };
  }

  // Mutation

  insertNewAfter(
    rangeSelection: RangeSelection,
    restoreSelection: boolean,
  ): ParagraphNode {
    const newElement = $createParagraphNode();
    newElement.setTextFormat(rangeSelection.format);
    newElement.setTextStyle(rangeSelection.style);
    const direction = this.getDirection();
    newElement.setDirection(direction);
    newElement.setStyle(this.getTextStyle());
    this.insertAfter(newElement, restoreSelection);
    return newElement;
  }

  collapseAtStart(): boolean {
    const children = this.getChildren();
    // If we have an empty (trimmed) first paragraph and try and remove it,
    // delete the paragraph as long as we have another sibling to go to
    if (
      children.length === 0 ||
      ($isTextNode(children[0]) && children[0].getTextContent().trim() === '')
    ) {
      const nextSibling = this.getNextSibling();
      if (nextSibling !== null) {
        this.selectNext();
        this.remove();
        return true;
      }
      const prevSibling = this.getPreviousSibling();
      if (prevSibling !== null) {
        this.selectPrevious();
        this.remove();
        return true;
      }
    }
    return false;
  }
}

function $convertParagraphElement(element: HTMLElement): DOMConversionOutput {
  const node = $createParagraphNode();
  setCommonBlockPropsFromElement(element, node);
  return {node};
}

export function $createParagraphNode(): ParagraphNode {
  return $applyNodeReplacement(new ParagraphNode());
}

export function $isParagraphNode(
  node: LexicalNode | null | undefined,
): node is ParagraphNode {
  return node instanceof ParagraphNode;
}
