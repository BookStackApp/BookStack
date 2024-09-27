import {
    $createParagraphNode,
    $isElementNode,
    $isLineBreakNode,
    $isTextNode,
    DOMConversionMap,
    DOMConversionOutput,
    DOMExportOutput,
    EditorConfig,
    LexicalEditor,
    LexicalNode,
    Spread
} from "lexical";

import {
    $createTableCellNode,
    $isTableCellNode,
    SerializedTableCellNode,
    TableCellHeaderStates,
    TableCellNode
} from "@lexical/table";
import {TableCellHeaderState} from "@lexical/table/LexicalTableCellNode";
import {extractStyleMapFromElement, StyleMap} from "../utils/dom";
import {CommonBlockAlignment, extractAlignmentFromElement} from "./_common";

export type SerializedCustomTableCellNode = Spread<{
    styles: Record<string, string>;
    alignment: CommonBlockAlignment;
}, SerializedTableCellNode>

export class CustomTableCellNode extends TableCellNode {
    __styles: StyleMap = new Map;
    __alignment: CommonBlockAlignment = '';

    static getType(): string {
        return 'custom-table-cell';
    }

    static clone(node: CustomTableCellNode): CustomTableCellNode {
        const cellNode = new CustomTableCellNode(
            node.__headerState,
            node.__colSpan,
            node.__width,
            node.__key,
        );
        cellNode.__rowSpan = node.__rowSpan;
        cellNode.__styles = new Map(node.__styles);
        cellNode.__alignment = node.__alignment;
        return cellNode;
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

    createDOM(config: EditorConfig): HTMLElement {
        const element = super.createDOM(config);

        for (const [name, value] of this.__styles.entries()) {
            element.style.setProperty(name, value);
        }

        if (this.__alignment) {
            element.classList.add('align-' + this.__alignment);
        }

        return element;
    }

    updateDOM(prevNode: CustomTableCellNode): boolean {
        return super.updateDOM(prevNode)
            || this.__styles !== prevNode.__styles
            || this.__alignment !== prevNode.__alignment;
    }

    static importDOM(): DOMConversionMap | null {
        return {
            td: (node: Node) => ({
                conversion: $convertCustomTableCellNodeElement,
                priority: 0,
            }),
            th: (node: Node) => ({
                conversion: $convertCustomTableCellNodeElement,
                priority: 0,
            }),
        };
    }

    exportDOM(editor: LexicalEditor): DOMExportOutput {
        const element = this.createDOM(editor._config);
        return {
            element
        };
    }

    static importJSON(serializedNode: SerializedCustomTableCellNode): CustomTableCellNode {
        const node = $createCustomTableCellNode(
            serializedNode.headerState,
            serializedNode.colSpan,
            serializedNode.width,
        );

        node.setStyles(new Map(Object.entries(serializedNode.styles)));
        node.setAlignment(serializedNode.alignment);

        return node;
    }

    exportJSON(): SerializedCustomTableCellNode {
        return {
            ...super.exportJSON(),
            type: 'custom-table-cell',
            styles: Object.fromEntries(this.__styles),
            alignment: this.__alignment,
        };
    }
}

function $convertCustomTableCellNodeElement(domNode: Node): DOMConversionOutput {
    const output =  $convertTableCellNodeElement(domNode);

    if (domNode instanceof HTMLElement && output.node instanceof CustomTableCellNode) {
        output.node.setStyles(extractStyleMapFromElement(domNode));
        output.node.setAlignment(extractAlignmentFromElement(domNode));
    }

    return output;
}

/**
 * Function taken from:
 * https://github.com/facebook/lexical/blob/e1881a6e409e1541c10dd0b5378f3a38c9dc8c9e/packages/lexical-table/src/LexicalTableCellNode.ts#L289
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 * MIT LICENSE
 * Modified since copy.
 */
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


export function $createCustomTableCellNode(
    headerState: TableCellHeaderState = TableCellHeaderStates.NO_STATUS,
    colSpan = 1,
    width?: number,
): CustomTableCellNode {
    return new CustomTableCellNode(headerState, colSpan, width);
}

export function $isCustomTableCellNode(node: LexicalNode | null | undefined): node is CustomTableCellNode {
    return node instanceof CustomTableCellNode;
}