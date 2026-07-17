import {sizeToPixels} from "../../../utils/dom";
import {SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";

export type CommonBlockAlignment = 'left' | 'right' | 'center' | 'justify' | '';
const validAlignments: CommonBlockAlignment[] = ['left', 'right', 'center', 'justify'];

type EditorNodeDirection = 'ltr' | 'rtl' | null;

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

export interface NodeHasDirection {
    readonly __dir: EditorNodeDirection;
    setDirection(direction: EditorNodeDirection): void;
    getDirection(): EditorNodeDirection;
}

export interface CommonBlockInterface extends NodeHasId, NodeHasAlignment, NodeHasInset, NodeHasDirection {}

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

export function extractDirectionFromElement(element: HTMLElement): EditorNodeDirection {
    const elemDir = (element.dir || '').toLowerCase();
    if (elemDir === 'rtl' || elemDir === 'ltr') {
        return elemDir;
    }

    return null;
}

export function setCommonBlockPropsFromElement(element: HTMLElement, node: CommonBlockInterface): void {
    if (element.id) {
        node.setId(element.id);
    }

    node.setAlignment(extractAlignmentFromElement(element));
    node.setInset(extractInsetFromElement(element));
    node.setDirection(extractDirectionFromElement(element));
}

export function commonPropertiesDifferent(nodeA: CommonBlockInterface, nodeB: CommonBlockInterface): boolean {
    return nodeA.__id !== nodeB.__id ||
        nodeA.__alignment !== nodeB.__alignment ||
        nodeA.__inset !== nodeB.__inset ||
        nodeA.__dir !== nodeB.__dir;
}

export function applyCommonPropertyChanges(prevNode: CommonBlockInterface, currentNode: CommonBlockInterface, element: HTMLElement): void {
    if (prevNode.__id !== currentNode.__id) {
        element.setAttribute('id', currentNode.__id);
    }

    if (prevNode.__alignment !== currentNode.__alignment) {
        for (const alignment of validAlignments) {
            element.classList.remove('align-' + alignment);
        }

        if (currentNode.__alignment) {
            element.classList.add('align-' + currentNode.__alignment);
        }
    }

    if (prevNode.__inset !== currentNode.__inset) {
        if (currentNode.__inset) {
            element.style.paddingLeft = `${currentNode.__inset}px`;
        } else {
            element.style.removeProperty('paddingLeft');
        }
    }

    if (prevNode.__dir !== currentNode.__dir) {
        if (currentNode.__dir) {
            element.dir = currentNode.__dir;
        } else {
            element.removeAttribute('dir');
        }
    }
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

    if (node.__dir) {
        element.dir = node.__dir;
    }
}

export function deserializeCommonBlockNode(serializedNode: SerializedCommonBlockNode, node: CommonBlockInterface): void {
    node.setId(serializedNode.id);
    node.setAlignment(serializedNode.alignment);
    node.setInset(serializedNode.inset);
    node.setDirection(serializedNode.direction);
}

export interface NodeHasSize {
    setHeight(height: number): void;
    setWidth(width: number): void;
    getHeight(): number;
    getWidth(): number;
}