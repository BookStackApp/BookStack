import {
    DecoratorNode,
    DOMConversion,
    DOMConversionMap,
    DOMConversionOutput,
    LexicalEditor,
    SerializedLexicalNode,
    Spread
} from "lexical";
import type {EditorConfig} from "lexical/LexicalEditor";
import {EditorDecoratorAdapter} from "../../ui/framework/decorator";
import {el} from "../../utils/dom";

export type SerializedDiagramNode = Spread<{
    id: string;
    drawingId: string;
    drawingUrl: string;
}, SerializedLexicalNode>

export class DiagramNode extends DecoratorNode<EditorDecoratorAdapter> {
    __id: string = '';
    __drawingId: string = '';
    __drawingUrl: string = '';

    static getType(): string {
        return 'diagram';
    }

    static clone(node: DiagramNode): DiagramNode {
        const newNode = new DiagramNode(node.__drawingId, node.__drawingUrl);
        newNode.__id = node.__id;
        return newNode;
    }

    constructor(drawingId: string, drawingUrl: string, key?: string) {
        super(key);
        this.__drawingId = drawingId;
        this.__drawingUrl = drawingUrl;
    }

    setDrawingIdAndUrl(drawingId: string, drawingUrl: string): void {
        const self = this.getWritable();
        self.__drawingUrl = drawingUrl;
        self.__drawingId = drawingId;
    }

    getDrawingIdAndUrl(): { id: string, url: string } {
        const self = this.getLatest();
        return {
            id: self.__drawingId,
            url: self.__drawingUrl,
        };
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    decorate(editor: LexicalEditor, config: EditorConfig): EditorDecoratorAdapter {
        return {
            type: 'diagram',
            getNode: () => this,
        };
    }

    isInline(): boolean {
        return false;
    }

    isIsolated() {
        return true;
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        return el('div', {
            id: this.__id || null,
            'drawio-diagram': this.__drawingId,
        }, [
            el('img', {src: this.__drawingUrl}),
        ]);
    }

    updateDOM(prevNode: DiagramNode, dom: HTMLElement) {
        const img = dom.querySelector('img');
        if (!img) return false;

        if (prevNode.__id !== this.__id) {
            dom.setAttribute('id', this.__id);
        }

        if (prevNode.__drawingUrl !== this.__drawingUrl) {
            img.setAttribute('src', this.__drawingUrl);
        }

        if (prevNode.__drawingId !== this.__drawingId) {
            dom.setAttribute('drawio-diagram', this.__drawingId);
        }

        return false;
    }

    static importDOM(): DOMConversionMap | null {
        return {
            div(node: HTMLElement): DOMConversion | null {

                if (!node.hasAttribute('drawio-diagram')) {
                    return null;
                }

                return {
                    conversion: (element: HTMLElement): DOMConversionOutput | null => {

                        const img = element.querySelector('img');
                        const drawingUrl = img?.getAttribute('src') || '';
                        const drawingId = element.getAttribute('drawio-diagram') || '';
                        const node = $createDiagramNode(drawingId, drawingUrl);

                        if (element.id) {
                            node.setId(element.id);
                        }

                        return { node };
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedDiagramNode {
        return {
            type: 'diagram',
            version: 1,
            id: this.__id,
            drawingId: this.__drawingId,
            drawingUrl: this.__drawingUrl,
        };
    }

    static importJSON(serializedNode: SerializedDiagramNode): DiagramNode {
        const node = $createDiagramNode(serializedNode.drawingId, serializedNode.drawingUrl);
        node.setId(serializedNode.id || '');
        return node;
    }
}

export function $createDiagramNode(drawingId: string = '', drawingUrl: string = ''): DiagramNode {
    return new DiagramNode(drawingId, drawingUrl);
}
