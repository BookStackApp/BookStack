import {
    DecoratorNode,
    DOMConversion,
    DOMConversionMap,
    DOMConversionOutput,
    LexicalEditor, LexicalNode,
    SerializedLexicalNode,
    Spread
} from "lexical";
import type {EditorConfig} from "lexical/LexicalEditor";
import {EditorDecoratorAdapter} from "../ui/framework/decorator";
import {el} from "../utils/dom";
import {CommonBlockAlignment, extractAlignmentFromElement} from "./_common";

export interface ImageNodeOptions {
    alt?: string;
    width?: number;
    height?: number;
}

export type SerializedImageNode = Spread<{
    src: string;
    alt: string;
    width: number;
    height: number;
    alignment: CommonBlockAlignment;
}, SerializedLexicalNode>

export class ImageNode extends DecoratorNode<EditorDecoratorAdapter> {
    __src: string = '';
    __alt: string = '';
    __width: number = 0;
    __height: number = 0;
    __alignment: CommonBlockAlignment = '';

    static getType(): string {
        return 'image';
    }

    static clone(node: ImageNode): ImageNode {
        return new ImageNode(node.__src, {
            alt: node.__alt,
            width: node.__width,
            height: node.__height,
        });
    }

    constructor(src: string, options: ImageNodeOptions, key?: string) {
        super(key);
        this.__src = src;
        if (options.alt) {
            this.__alt = options.alt;
        }
        if (options.width) {
            this.__width = options.width;
        }
        if (options.height) {
            this.__height = options.height;
        }
    }

    setSrc(src: string): void {
        const self = this.getWritable();
        self.__src = src;
    }

    getSrc(): string {
        const self = this.getLatest();
        return self.__src;
    }

    setAltText(altText: string): void {
        const self = this.getWritable();
        self.__alt = altText;
    }

    getAltText(): string {
        const self = this.getLatest();
        return self.__alt;
    }

    setHeight(height: number): void {
        const self = this.getWritable();
        self.__height = height;
    }

    getHeight(): number {
        const self = this.getLatest();
        return self.__height;
    }

    setWidth(width: number): void {
        const self = this.getWritable();
        self.__width = width;
    }

    getWidth(): number {
        const self = this.getLatest();
        return self.__width;
    }

    setAlignment(alignment: CommonBlockAlignment) {
        const self = this.getWritable();
        self.__alignment = alignment;
    }

    getAlignment(): CommonBlockAlignment {
        const self = this.getLatest();
        return self.__alignment;
    }

    isInline(): boolean {
        return true;
    }

    decorate(editor: LexicalEditor, config: EditorConfig): EditorDecoratorAdapter {
        return {
            type: 'image',
            getNode: () => this,
        };
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const element = document.createElement('img');
        element.setAttribute('src', this.__src);

        if (this.__width) {
            element.setAttribute('width', String(this.__width));
        }
        if (this.__height) {
            element.setAttribute('height', String(this.__height));
        }
        if (this.__alt) {
            element.setAttribute('alt', this.__alt);
        }

        if (this.__alignment) {
            element.classList.add('align-' + this.__alignment);
        }

        return el('span', {class: 'editor-image-wrap'}, [
            element,
        ]);
    }

    updateDOM(prevNode: ImageNode, dom: HTMLElement) {
        const image = dom.querySelector('img');
        if (!image) return false;

        if (prevNode.__src !== this.__src) {
            image.setAttribute('src', this.__src);
        }

        if (prevNode.__width !== this.__width) {
            if (this.__width) {
                image.setAttribute('width', String(this.__width));
            } else {
                image.removeAttribute('width');
            }
        }

        if (prevNode.__height !== this.__height) {
            if (this.__height) {
                image.setAttribute('height', String(this.__height));
            } else {
                image.removeAttribute('height');
            }
        }

        if (prevNode.__alt !== this.__alt) {
            if (this.__alt) {
                image.setAttribute('alt', String(this.__alt));
            } else {
                image.removeAttribute('alt');
            }
        }

        if (prevNode.__alignment !== this.__alignment) {
            if (prevNode.__alignment) {
                image.classList.remove('align-' + prevNode.__alignment);
            }
            if (this.__alignment) {
                image.classList.add('align-' + this.__alignment);
            }
        }

        return false;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            img(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {

                        const src = element.getAttribute('src') || '';
                        const options: ImageNodeOptions = {
                            alt: element.getAttribute('alt') || '',
                            height: Number.parseInt(element.getAttribute('height') || '0'),
                            width: Number.parseInt(element.getAttribute('width') || '0'),
                        }

                        const node = new ImageNode(src, options);
                        node.setAlignment(extractAlignmentFromElement(element));

                        return { node };
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedImageNode {
        return {
            type: 'image',
            version: 1,
            src: this.__src,
            alt: this.__alt,
            height: this.__height,
            width: this.__width,
            alignment: this.__alignment,
        };
    }

    static importJSON(serializedNode: SerializedImageNode): ImageNode {
        const node = $createImageNode(serializedNode.src, {
            alt: serializedNode.alt,
            width: serializedNode.width,
            height: serializedNode.height,
        });
        node.setAlignment(serializedNode.alignment);
        return node;
    }
}

export function $createImageNode(src: string, options: ImageNodeOptions = {}): ImageNode {
    return new ImageNode(src, options);
}

export function $isImageNode(node: LexicalNode | null | undefined) {
    return node instanceof ImageNode;
}