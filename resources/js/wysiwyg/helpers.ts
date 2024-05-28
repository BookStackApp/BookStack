import {$createParagraphNode, $getSelection, BaseSelection, LexicalEditor} from "lexical";
import {LexicalElementNodeCreator, LexicalNodeMatcher} from "./nodes";
import {$getNearestBlockElementAncestorOrThrow} from "@lexical/utils";
import {$setBlocksType} from "@lexical/selection";

export function selectionContainsNodeType(selection: BaseSelection|null, matcher: LexicalNodeMatcher): boolean {
    if (!selection) {
        return false;
    }

    for (const node of selection.getNodes()) {
        if (matcher(node)) {
            return true;
        }

        for (const parent of node.getParents()) {
            if (matcher(parent)) {
                return true;
            }
        }
    }

    return false;
}

export function toggleSelectionBlockNodeType(editor: LexicalEditor, matcher: LexicalNodeMatcher, creator: LexicalElementNodeCreator) {
    editor.update(() => {
        const selection = $getSelection();
        const blockElement = selection ? $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]) : null;
        if (selection && matcher(blockElement)) {
            $setBlocksType(selection, $createParagraphNode);
        } else {
            $setBlocksType(selection, creator);
        }
    });
}