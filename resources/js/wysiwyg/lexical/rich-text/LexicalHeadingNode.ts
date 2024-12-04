import {
    $applyNodeReplacement,
    $createParagraphNode,
    type DOMConversionMap,
    DOMConversionOutput,
    type DOMExportOutput,
    type EditorConfig,
    isHTMLElement,
    type LexicalEditor,
    type LexicalNode,
    type NodeKey,
    type ParagraphNode,
    type RangeSelection,
    type Spread
} from "lexical";
import {addClassNamesToElement} from "@lexical/utils";
import {CommonBlockNode, copyCommonBlockProperties, SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";
import {
    commonPropertiesDifferent, deserializeCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "lexical/nodes/common";

export type HeadingTagType = 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';

export type SerializedHeadingNode = Spread<
    {
        tag: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
    },
    SerializedCommonBlockNode
>;

/** @noInheritDoc */
export class HeadingNode extends CommonBlockNode {
    /** @internal */
    __tag: HeadingTagType;

    static getType(): string {
        return 'heading';
    }

    static clone(node: HeadingNode): HeadingNode {
        const clone = new HeadingNode(node.__tag, node.__key);
        copyCommonBlockProperties(node, clone);
        return clone;
    }

    constructor(tag: HeadingTagType, key?: NodeKey) {
        super(key);
        this.__tag = tag;
    }

    getTag(): HeadingTagType {
        return this.__tag;
    }

    // View

    createDOM(config: EditorConfig): HTMLElement {
        const tag = this.__tag;
        const element = document.createElement(tag);
        const theme = config.theme;
        const classNames = theme.heading;
        if (classNames !== undefined) {
            const className = classNames[tag];
            addClassNamesToElement(element, className);
        }
        updateElementWithCommonBlockProps(element, this);
        return element;
    }

    updateDOM(prevNode: HeadingNode, dom: HTMLElement): boolean {
        return commonPropertiesDifferent(prevNode, this);
    }

    static importDOM(): DOMConversionMap | null {
        return {
            h1: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h2: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h3: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h4: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h5: (node: Node) => ({
                conversion: $convertHeadingElement,
                priority: 0,
            }),
            h6: (node: Node) => ({
                conversion: $convertHeadingElement,
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

    static importJSON(serializedNode: SerializedHeadingNode): HeadingNode {
        const node = $createHeadingNode(serializedNode.tag);
        deserializeCommonBlockNode(serializedNode, node);
        return node;
    }

    exportJSON(): SerializedHeadingNode {
        return {
            ...super.exportJSON(),
            tag: this.getTag(),
            type: 'heading',
            version: 1,
        };
    }

    // Mutation
    insertNewAfter(
        selection?: RangeSelection,
        restoreSelection = true,
    ): ParagraphNode | HeadingNode {
        const anchorOffet = selection ? selection.anchor.offset : 0;
        const lastDesc = this.getLastDescendant();
        const isAtEnd =
            !lastDesc ||
            (selection &&
                selection.anchor.key === lastDesc.getKey() &&
                anchorOffet === lastDesc.getTextContentSize());
        const newElement =
            isAtEnd || !selection
                ? $createParagraphNode()
                : $createHeadingNode(this.getTag());
        const direction = this.getDirection();
        newElement.setDirection(direction);
        this.insertAfter(newElement, restoreSelection);
        if (anchorOffet === 0 && !this.isEmpty() && selection) {
            const paragraph = $createParagraphNode();
            paragraph.select();
            this.replace(paragraph, true);
        }
        return newElement;
    }

    collapseAtStart(): true {
        const newElement = !this.isEmpty()
            ? $createHeadingNode(this.getTag())
            : $createParagraphNode();
        const children = this.getChildren();
        children.forEach((child) => newElement.append(child));
        this.replace(newElement);
        return true;
    }

    extractWithChild(): boolean {
        return true;
    }
}

function $convertHeadingElement(element: HTMLElement): DOMConversionOutput {
    const nodeName = element.nodeName.toLowerCase();
    let node = null;
    if (
        nodeName === 'h1' ||
        nodeName === 'h2' ||
        nodeName === 'h3' ||
        nodeName === 'h4' ||
        nodeName === 'h5' ||
        nodeName === 'h6'
    ) {
        node = $createHeadingNode(nodeName);
        setCommonBlockPropsFromElement(element, node);
    }
    return {node};
}

export function $createHeadingNode(headingTag: HeadingTagType): HeadingNode {
    return $applyNodeReplacement(new HeadingNode(headingTag));
}

export function $isHeadingNode(
    node: LexicalNode | null | undefined,
): node is HeadingNode {
    return node instanceof HeadingNode;
}