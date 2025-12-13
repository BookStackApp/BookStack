import {
    $getSelection, $isRangeSelection,
    COMMAND_PRIORITY_NORMAL, RangeSelection, TextNode
} from "lexical";
import {KEY_AT_COMMAND} from "lexical/LexicalCommands";
import {$createMentionNode} from "@lexical/link/LexicalMentionNode";
import {EditorUiContext} from "../ui/framework/core";
import {MentionDecorator} from "../ui/decorators/MentionDecorator";


function enterUserSelectMode(context: EditorUiContext, selection: RangeSelection) {
    const textNode = selection.getNodes()[0] as TextNode;
    const selectionPos = selection.getStartEndPoints();
    if (!selectionPos) {
        return;
    }

    const offset = selectionPos[0].offset;

    // Ignore if the @ sign is not after a space or the start of the line
    const atStart = offset === 0;
    const afterSpace = textNode.getTextContent().charAt(offset - 1) === ' ';
    if (!atStart && !afterSpace) {
        return;
    }

    const split = textNode.splitText(offset);
    const newNode = split[atStart ? 0 : 1];

    const mention = $createMentionNode(0, '', '');
    newNode.replace(mention);

    requestAnimationFrame(() => {
        const mentionDecorator = context.manager.getDecoratorByNodeKey(mention.getKey());
        if (mentionDecorator instanceof MentionDecorator) {
            mentionDecorator.showSelection()
        }
    });
}

export function registerMentions(context: EditorUiContext): () => void {
    const editor = context.editor;

    const unregisterCommand = editor.registerCommand(KEY_AT_COMMAND, function (event: KeyboardEvent): boolean {
        const selection = $getSelection();
        if ($isRangeSelection(selection) && selection.isCollapsed()) {
            window.setTimeout(() => {
                editor.update(() => {
                    enterUserSelectMode(context, selection);
                });
            }, 1);
        }
        return false;
    }, COMMAND_PRIORITY_NORMAL);

    return (): void => {
        unregisterCommand();
    };
}