import {
    $createParagraphNode, $getSelection,
    $insertNodes,
    $isDecoratorNode,
    $isRangeSelection, $isTextNode, $setSelection, COMMAND_PRIORITY_HIGH, DRAGSTART_COMMAND, DROP_COMMAND,
    LexicalEditor,
    LexicalNode, PASTE_COMMAND
} from "lexical";
import {$getBlockElementNodesInSelection, $insertNewNodesAtSelection, $selectSingleNode} from "../utils/selection";
import {$getNodePositionFromMouseEvent, $htmlToBlockNodes, $htmlToNodes} from "../utils/nodes";
import {Clipboard} from "../../services/clipboard";
import {$createImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$createLinkNode} from "@lexical/link";
import {EditorImageData, uploadImageFile} from "../utils/images";
import {EditorUiContext} from "../ui/framework/core";
import {$getHtmlContent} from "@lexical/clipboard";

const internalActiveDragTracker: WeakMap<LexicalEditor, DragEvent>  = new WeakMap();

function $insertNodesAtEvent(nodes: LexicalNode[], event: DragEvent, editor: LexicalEditor) {
    const position = $getNodePositionFromMouseEvent(event, editor);

    if (position && $isTextNode(position.node)) {
        const selection = position.node.select(position.offset, position.offset);
        $setSelection(selection);
    } else if  (position) {
        $selectSingleNode(position.node);
    }

    $insertNewNodesAtSelection(nodes);

    if (position) {
        if (!$isDecoratorNode(position.node) && !position.node?.getTextContent()) {
            position.node.remove();
        }
    }
}

async function insertTemplateToEditor(editor: LexicalEditor, templateId: string, event: DragEvent) {
    const resp = await window.$http.get(`/templates/${templateId}`);
    const data = (resp.data || {html: ''}) as {html: string}
    const html: string = data.html || '';

    editor.update(() => {
        const newNodes = $htmlToBlockNodes(editor, html);
        $insertNodesAtEvent(newNodes, event, editor);
    });
}

function insertHtmlToEditor(editor: LexicalEditor, html: string, isFromInternal: boolean, event: DragEvent) {
    editor.update(() => {
        if (isFromInternal) {
            const selected = $getSelection();
            if ($isRangeSelection(selected)) {
                selected.removeText();
                const selectionBlocks = $getBlockElementNodesInSelection(selected);
                for (const block of selectionBlocks) {
                    if (block.isEmpty()) {
                        block.remove();
                    }
                }
            }
        }

        const newNodes = $htmlToNodes(editor, html);
        $insertNodesAtEvent(newNodes, event, editor);
    });
}

function handleMediaInsert(data: DataTransfer, context: EditorUiContext): boolean {
    const clipboard = new Clipboard(data);
    let handled = false;

    // Don't handle the event ourselves if no items exist of contains table-looking data
    if (!clipboard.hasItems() || clipboard.containsTabularData()) {
        return handled;
    }

    const images = clipboard.getImages();
    if (images.length > 0) {
        handled = true;
    }

    context.editor.update(async () => {
        for (const imageFile of images) {
            const loadingImage = window.baseUrl('/loading.gif');
            const loadingNode = $createImageNode(loadingImage);
            const imageWrap = $createParagraphNode();
            imageWrap.append(loadingNode);
            $insertNodes([imageWrap]);

            try {
                const respData: EditorImageData = await uploadImageFile(imageFile, context.options.pageId);
                const safeName = respData.name.replace(/"/g, '');
                context.editor.update(() => {
                    const finalImage = $createImageNode(respData.thumbs?.display || '', {
                        alt: safeName,
                    });
                    const imageLink = $createLinkNode(respData.url, {target: '_blank'});
                    imageLink.append(finalImage);
                    loadingNode.replace(imageLink);
                });
            } catch (err: any) {
                context.editor.update(() => {
                    loadingNode.remove(false);
                });
                window.$events.error(err?.data?.message || context.options.translations.imageUploadErrorText);
                console.error(err);
            }
        }
    });

    return handled;
}

function handleImageLinkInsert(data: DataTransfer, context: EditorUiContext): boolean {
    const regex = /https?:\/\/([^?#]*?)\.(png|jpeg|jpg|gif|webp|bmp|avif)/i
    const text = data.getData('text/plain');
    if (text && regex.test(text)) {
        context.editor.update(() => {
            const image = $createImageNode(text);
            $insertNodes([image]);
            image.select();
        });
        return true;
    }

    return false;
}

function createDropListener(context: EditorUiContext): (event: DragEvent) => boolean {
    const editor = context.editor;
    return (event: DragEvent): boolean => {

        const hadInternalActiveDrag = internalActiveDragTracker.has(editor);
        internalActiveDragTracker.delete(editor);

        // Template handling
        const templateId = event.dataTransfer?.getData('bookstack/template') || '';
        if (templateId) {
            insertTemplateToEditor(editor, templateId, event);
            event.preventDefault();
            event.stopPropagation();
            return true;
        }

        // HTML contents drop
        const html = event.dataTransfer?.getData('text/html') || '';
        if (html) {
            insertHtmlToEditor(editor, html, hadInternalActiveDrag, event);
            event.preventDefault();
            event.stopPropagation();
            return true;
        }

        if (event.dataTransfer) {
            const handled = handleMediaInsert(event.dataTransfer, context);
            if (handled) {
                event.preventDefault();
                event.stopPropagation();
                return true;
            }
        }

        return false;
    };
}

function createPasteListener(context: EditorUiContext): (event: ClipboardEvent) => boolean {
    return (event: ClipboardEvent) => {
        if (!event.clipboardData) {
            return false;
        }

        const handled =
            handleImageLinkInsert(event.clipboardData, context) ||
            handleMediaInsert(event.clipboardData, context);

        if (handled) {
            event.preventDefault();
        }

        return handled;
    };
}

function createDragStartListener(context: EditorUiContext): (event: DragEvent) => boolean {
    return (event: DragEvent) => {
        // Track when drag events are started internally from the editor
        internalActiveDragTracker.set(context.editor, event);

        // If an internal range selection, serialize the range contents
        // fully as output HTML, instead of editor HTML
        context.editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                selection.extract();
                const html = $getHtmlContent(context.editor, selection);
                event.dataTransfer?.setData('text/html', html);
            }
        });
        return false;
    };
}

export function registerDropPasteHandling(context: EditorUiContext): () => void {
    const dropListener = createDropListener(context);
    const pasteListener = createPasteListener(context);
    const dragstartListener = createDragStartListener(context);

    const unregisterDrop = context.editor.registerCommand(DROP_COMMAND, dropListener, COMMAND_PRIORITY_HIGH);
    const unregisterPaste = context.editor.registerCommand(PASTE_COMMAND, pasteListener, COMMAND_PRIORITY_HIGH);
    const unregisterDragStart = context.editor.registerCommand(DRAGSTART_COMMAND, dragstartListener, COMMAND_PRIORITY_HIGH);
    context.scrollDOM.addEventListener('drop', dropListener);

    return () => {
        unregisterDrop();
        unregisterPaste();
        unregisterDragStart();
        context.scrollDOM.removeEventListener('drop', dropListener);
    };
}