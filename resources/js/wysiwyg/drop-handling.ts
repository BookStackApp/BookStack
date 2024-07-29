import {
    $isDecoratorNode,
    LexicalEditor,
    LexicalNode
} from "lexical";
import {
    $getNearestBlockNodeForCoords,
    $htmlToBlockNodes,
    $insertNewBlockNodesAtSelection,
    $selectSingleNode
} from "./helpers";

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

function createDropListener(editor: LexicalEditor): (event: DragEvent) => void {
    return (event: DragEvent) => {
        // Template handling
        const templateId = event.dataTransfer?.getData('bookstack/template') || '';
        if (templateId) {
            insertTemplateToEditor(editor, templateId, event);
            event.preventDefault();
            return;
        }

        // HTML contents drop
        const html = event.dataTransfer?.getData('text/html') || '';
        if (html) {
            editor.update(() => {
                const newNodes = $htmlToBlockNodes(editor, html);
                $insertNodesAtEvent(newNodes, event, editor);
            });
            event.preventDefault();
            return;
        }
    };
}

export function handleDropEvents(editor: LexicalEditor) {
    const dropListener = createDropListener(editor);

    editor.registerRootListener((rootElement, prevRootElement) => {
        rootElement?.addEventListener('drop', dropListener);
        prevRootElement?.removeEventListener('drop', dropListener);
    });
}