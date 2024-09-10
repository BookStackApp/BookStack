import {LexicalNode, Spread} from "lexical";
import type {SerializedElementNode} from "lexical/nodes/LexicalElementNode";
import {sizeToPixels} from "../utils/dom";

export type CommonBlockAlignment = 'left' | 'right' | 'center' | 'justify' | '';
const validAlignments: CommonBlockAlignment[] = ['left', 'right', 'center', 'justify'];

export type SerializedCommonBlockNode = Spread<{
    id: string;
    alignment: CommonBlockAlignment;
    inset: number;
}, SerializedElementNode>

export interface NodeHasAlignment {
    readonly __alignment: CommonBlockAlignment;
    setAlignment(alignment: CommonBlockAlignment): void;
    getAlignment(): CommonBlockAlignment;
}

export interface NodeHasId {
    readonly __id: string;
    setId(id: string): void;
    getId(): string;
}

export interface NodeHasInset {
    readonly __inset: number;
    setInset(inset: number): void;
    getInset(): number;
}

interface CommonBlockInterface extends NodeHasId, NodeHasAlignment, NodeHasInset {}

export function extractAlignmentFromElement(element: HTMLElement): CommonBlockAlignment {
    const textAlignStyle: string = element.style.textAlign || '';
    if (validAlignments.includes(textAlignStyle as CommonBlockAlignment)) {
        return textAlignStyle as CommonBlockAlignment;
    }

    if (element.classList.contains('align-left')) {
        return 'left';
    } else if (element.classList.contains('align-right')) {
        return 'right'
    } else if (element.classList.contains('align-center')) {
        return 'center'
    } else if (element.classList.contains('align-justify')) {
        return 'justify'
    }

    return '';
}

export function extractInsetFromElement(element: HTMLElement): number {
    const elemPadding: string = element.style.paddingLeft || '0';
    return sizeToPixels(elemPadding);
}

export function setCommonBlockPropsFromElement(element: HTMLElement, node: CommonBlockInterface): void {
    if (element.id) {
        node.setId(element.id);
    }

    node.setAlignment(extractAlignmentFromElement(element));
    node.setInset(extractInsetFromElement(element));
}

export function commonPropertiesDifferent(nodeA: CommonBlockInterface, nodeB: CommonBlockInterface): boolean {
    return nodeA.__id !== nodeB.__id ||
        nodeA.__alignment !== nodeB.__alignment ||
        nodeA.__inset !== nodeB.__inset;
}

export function updateElementWithCommonBlockProps(element: HTMLElement, node: CommonBlockInterface): void {
    if (node.__id) {
        element.setAttribute('id', node.__id);
    }

    if (node.__alignment) {
        element.classList.add('align-' + node.__alignment);
    }

    if (node.__inset) {
        element.style.paddingLeft = `${node.__inset}px`;
    }
}

export function deserializeCommonBlockNode(serializedNode: SerializedCommonBlockNode, node: CommonBlockInterface): void {
    node.setId(serializedNode.id);
    node.setAlignment(serializedNode.alignment);
    node.setInset(serializedNode.inset);
}

export interface NodeHasSize {
    setHeight(height: number): void;
    setWidth(width: number): void;
    getHeight(): number;
    getWidth(): number;
}