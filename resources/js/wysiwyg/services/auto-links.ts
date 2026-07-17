import {
    $getSelection, BaseSelection,
    COMMAND_PRIORITY_NORMAL,
    KEY_ENTER_COMMAND,
    KEY_SPACE_COMMAND,
    LexicalEditor,
    TextNode
} from "lexical";
import {$getTextNodeFromSelection} from "../utils/selection";
import {$createLinkNode, LinkNode} from "@lexical/link";


function isLinkText(text: string): boolean {
    const lower = text.toLowerCase();
    if (!lower.startsWith('http')) {
        return false;
    }

    const linkRegex = /(http|https):\/\/(\S+)\.\S+$/;
    return linkRegex.test(text);
}


function handlePotentialLinkEvent(node: TextNode, selection: BaseSelection, editor: LexicalEditor) {
    const selectionRange = selection.getStartEndPoints();
    if (!selectionRange) {
        return;
    }

    const cursorPoint = selectionRange[0].offset;
    const nodeText = node.getTextContent();
    const rTrimText = nodeText.slice(0, cursorPoint);
    const priorSpaceIndex = rTrimText.lastIndexOf(' ');
    const startIndex = priorSpaceIndex + 1;
    const textSegment = nodeText.slice(startIndex, cursorPoint);

    if (!isLinkText(textSegment)) {
        return;
    }

    editor.update(() => {
        const linkNode: LinkNode = $createLinkNode(textSegment);
        linkNode.append(new TextNode(textSegment));

        const splits = node.splitText(startIndex, cursorPoint);
        const targetIndex = startIndex > 0 ? 1 : 0;
        const targetText = splits[targetIndex];
        if (targetText) {
            targetText.replace(linkNode);
        }
    });
}


export function registerAutoLinks(editor: LexicalEditor): () => void {

    const handler = (payload: KeyboardEvent): boolean => {
        const selection = $getSelection();
        const textNode = $getTextNodeFromSelection(selection);
        if (textNode && selection) {
            handlePotentialLinkEvent(textNode, selection, editor);
        }

        return false;
    };

    const unregisterSpace = editor.registerCommand(KEY_SPACE_COMMAND, handler, COMMAND_PRIORITY_NORMAL);
    const unregisterEnter = editor.registerCommand(KEY_ENTER_COMMAND, handler, COMMAND_PRIORITY_NORMAL);

    return (): void => {
        unregisterSpace();
        unregisterEnter();
    };
}