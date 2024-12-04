import {
    $applyNodeReplacement,
    $createParagraphNode,
    type DOMConversionMap,
    type DOMConversionOutput,
    type DOMExportOutput,
    type EditorConfig,
    isHTMLElement,
    type LexicalEditor,
    LexicalNode,
    type NodeKey,
    type ParagraphNode,
    type RangeSelection
} from "lexical";
import {addClassNamesToElement} from "@lexical/utils";
import {CommonBlockNode, copyCommonBlockProperties, SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";
import {
    commonPropertiesDifferent, deserializeCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "lexical/nodes/common";

export type SerializedQuoteNode = SerializedCommonBlockNode;

/** @noInheritDoc */
export class QuoteNode extends CommonBlockNode {
    static getType(): string {
        return 'quote';
    }

    static clone(node: QuoteNode): QuoteNode {
        const clone = new QuoteNode(node.__key);
        copyCommonBlockProperties(node, clone);
        return clone;
    }

    constructor(key?: NodeKey) {
        super(key);
    }

    // View

    createDOM(config: EditorConfig): HTMLElement {
        const element = document.createElement('blockquote');
        addClassNamesToElement(element, config.theme.quote);
        updateElementWithCommonBlockProps(element, this);
        return element;
    }

    updateDOM(prevNode: QuoteNode, dom: HTMLElement): boolean {
        return commonPropertiesDifferent(prevNode, this);
    }

    static importDOM(): DOMConversionMap | null {
        return {
            blockquote: (node: Node) => ({
                conversion: $convertBlockquoteElement,
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

    static importJSON(serializedNode: SerializedQuoteNode): QuoteNode {
        const node = $createQuoteNode();
        deserializeCommonBlockNode(serializedNode, node);
        return node;
    }

    exportJSON(): SerializedQuoteNode {
        return {
            ...super.exportJSON(),
            type: 'quote',
        };
    }

    // Mutation

    insertNewAfter(_: RangeSelection, restoreSelection?: boolean): ParagraphNode {
        const newBlock = $createParagraphNode();
        const direction = this.getDirection();
        newBlock.setDirection(direction);
        this.insertAfter(newBlock, restoreSelection);
        return newBlock;
    }

    collapseAtStart(): true {
        const paragraph = $createParagraphNode();
        const children = this.getChildren();
        children.forEach((child) => paragraph.append(child));
        this.replace(paragraph);
        return true;
    }

    canMergeWhenEmpty(): true {
        return true;
    }
}

export function $createQuoteNode(): QuoteNode {
    return $applyNodeReplacement(new QuoteNode());
}

export function $isQuoteNode(
    node: LexicalNode | null | undefined,
): node is QuoteNode {
    return node instanceof QuoteNode;
}

function $convertBlockquoteElement(element: HTMLElement): DOMConversionOutput {
    const node = $createQuoteNode();
    setCommonBlockPropsFromElement(element, node);
    return {node};
}