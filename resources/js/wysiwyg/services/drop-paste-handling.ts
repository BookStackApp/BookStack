import {
    $createParagraphNode,
    $insertNodes,
    $isDecoratorNode, COMMAND_PRIORITY_HIGH, DROP_COMMAND,
    LexicalEditor,
    LexicalNode, PASTE_COMMAND
} from "lexical";
import {$insertNewBlockNodesAtSelection, $selectSingleNode} from "../utils/selection";
import {$getNearestBlockNodeForCoords, $htmlToBlockNodes} from "../utils/nodes";
import {Clipboard} from "../../services/clipboard";
import {$createImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$createLinkNode} from "@lexical/link";
import {EditorImageData, uploadImageFile} from "../utils/images";
import {EditorUiContext} from "../ui/framework/core";

function $getNodeFromMouseEvent(event: MouseEvent, editor: LexicalEditor): LexicalNode|null {
    const x = event.clientX;
    const y = event.clientY;
    const dom = document.elementFromPoint(x, y);
    if (!dom) {
        return null;
    }

    return $getNearestBlockNodeForCoords(editor, event.clientX, event.clientY);
}

function $insertNodesAtEvent(nodes: LexicalNode[], event: DragEvent, editor: LexicalEditor) {
    const positionNode = $getNodeFromMouseEvent(event, editor);

    if (positionNode) {
        $selectSingleNode(positionNode);
    }

    $insertNewBlockNodesAtSelection(nodes, true);

    if (!$isDecoratorNode(positionNode) || !positionNode?.getTextContent()) {
        positionNode?.remove();
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
            editor.update(() => {
                const newNodes = $htmlToBlockNodes(editor, html);
                $insertNodesAtEvent(newNodes, event, editor);
            });
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

export function registerDropPasteHandling(context: EditorUiContext): () => void {
    const dropListener = createDropListener(context);
    const pasteListener = createPasteListener(context);

    const unregisterDrop = context.editor.registerCommand(DROP_COMMAND, dropListener, COMMAND_PRIORITY_HIGH);
    const unregisterPaste = context.editor.registerCommand(PASTE_COMMAND, pasteListener, COMMAND_PRIORITY_HIGH);
    context.scrollDOM.addEventListener('drop', dropListener);

    return () => {
        unregisterDrop();
        unregisterPaste();
        context.scrollDOM.removeEventListener('drop', dropListener);
    };
}