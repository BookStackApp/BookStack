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
import * as DrawIO from '../../services/drawio';
import {EditorUiContext} from "../ui/framework/core";
import {HttpError} from "../../services/http";
import {el} from "../utils/dom";

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
        return new DiagramNode(node.__drawingId, node.__drawingUrl);
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

                        return {
                            node: $createDiagramNode(drawingId, drawingUrl),
                        };
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

export function $isDiagramNode(node: LexicalNode | null | undefined) {
    return node instanceof DiagramNode;
}


function handleUploadError(error: HttpError, context: EditorUiContext): void {
    if (error.status === 413) {
        window.$events.emit('error', context.options.translations.serverUploadLimitText || '');
    } else {
        window.$events.emit('error', context.options.translations.imageUploadErrorText || '');
    }
    console.error(error);
}

async function loadDiagramIdFromNode(editor: LexicalEditor, node: DiagramNode): Promise<string> {
    const drawingId = await new Promise<string>((res, rej) => {
        editor.getEditorState().read(() => {
            const {id: drawingId} = node.getDrawingIdAndUrl();
            res(drawingId);
        });
    });

    return drawingId || '';
}

async function updateDrawingNodeFromData(context: EditorUiContext, node: DiagramNode, pngData: string, isNew: boolean): Promise<void> {
    DrawIO.close();

    if (isNew) {
        const loadingImage: string = window.baseUrl('/loading.gif');
        context.editor.update(() => {
            node.setDrawingIdAndUrl('', loadingImage);
        });
    }

    try {
        const img = await DrawIO.upload(pngData, context.options.pageId);
        context.editor.update(() => {
            node.setDrawingIdAndUrl(String(img.id), img.url);
        });
    } catch (err) {
        if (err instanceof HttpError) {
            handleUploadError(err, context);
        }

        if (isNew) {
            context.editor.update(() => {
                node.remove();
            });
        }

        throw new Error(`Failed to save image with error: ${err}`);
    }
}

export function $openDrawingEditorForNode(context: EditorUiContext, node: DiagramNode): void {
    let isNew = false;
    DrawIO.show(context.options.drawioUrl, async () => {
        const drawingId = await loadDiagramIdFromNode(context.editor, node);
        isNew = !drawingId;
        return isNew ? '' : DrawIO.load(drawingId);
    }, async (pngData: string) => {
        return updateDrawingNodeFromData(context, node, pngData, isNew);
    });
}