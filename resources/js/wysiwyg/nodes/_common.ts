import {LexicalNode, Spread} from "lexical";
import type {SerializedElementNode} from "lexical/nodes/LexicalElementNode";

export type CommonBlockAlignment = 'left' | 'right' | 'center' | 'justify' | '';
const validAlignments: CommonBlockAlignment[] = ['left', 'right', 'center', 'justify'];

export type SerializedCommonBlockNode = Spread<{
    id: string;
    alignment: CommonBlockAlignment;
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

interface CommonBlockInterface extends NodeHasId, NodeHasAlignment {}

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

export function setCommonBlockPropsFromElement(element: HTMLElement, node: CommonBlockInterface): void {
    if (element.id) {
        node.setId(element.id);
    }

    node.setAlignment(extractAlignmentFromElement(element));
}

export function commonPropertiesDifferent(nodeA: CommonBlockInterface, nodeB: CommonBlockInterface): boolean {
    return nodeA.__id !== nodeB.__id ||
        nodeA.__alignment !== nodeB.__alignment;
}

export function updateElementWithCommonBlockProps(element: HTMLElement, node: CommonBlockInterface): void {
    if (node.__id) {
        element.setAttribute('id', node.__id);
    }

    if (node.__alignment) {
        element.classList.add('align-' + node.__alignment);
    }
}

export interface NodeHasSize {
    setHeight(height: number): void;
    setWidth(width: number): void;
    getHeight(): number;
    getWidth(): number;
}