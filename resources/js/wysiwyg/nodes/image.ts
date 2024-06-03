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
import {el} from "../helpers";

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
}, SerializedLexicalNode>

export class ImageNode extends DecoratorNode<HTMLElement> {
    __src: string = '';
    __alt: string = '';
    __width: number = 0;
    __height: number = 0;
    // TODO - Alignment

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

    isInline(): boolean {
        return true;
    }

    decorate(editor: LexicalEditor, config: EditorConfig): HTMLElement {
        console.log('decorate!');
        return el('div', {
            class: 'editor-image-decorator',
        }, ['decoration!!!']);
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const element = document.createElement('img');
        element.setAttribute('src', this.__src);
        element.textContent

        if (this.__width) {
            element.setAttribute('width', String(this.__width));
        }
        if (this.__height) {
            element.setAttribute('height', String(this.__height));
        }
        if (this.__alt) {
            element.setAttribute('alt', this.__alt);
        }
        return el('span', {class: 'editor-image-wrap'}, [
            element,
        ]);
    }

    updateDOM(prevNode: unknown, dom: HTMLElement) {
        // Returning false tells Lexical that this node does not need its
        // DOM element replacing with a new copy from createDOM.
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

                        return {
                            node: new ImageNode(src, options),
                        };
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
            width: this.__width
        };
    }

    static importJSON(serializedNode: SerializedImageNode): ImageNode {
        return $createImageNode(serializedNode.src, {
            alt: serializedNode.alt,
            width: serializedNode.width,
            height: serializedNode.height,
        });
    }
}

export function $createImageNode(src: string, options: ImageNodeOptions = {}): ImageNode {
    return new ImageNode(src, options);
}

export function $isImageNode(node: LexicalNode | null | undefined) {
    return node instanceof ImageNode;
}