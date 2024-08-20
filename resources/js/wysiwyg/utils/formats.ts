import {$isQuoteNode, HeadingNode, HeadingTagType} from "@lexical/rich-text";
import {$getSelection, LexicalEditor, LexicalNode} from "lexical";
import {
    $getBlockElementNodesInSelection,
    $getNodeFromSelection,
    $insertNewBlockNodeAtSelection,
    $toggleSelectionBlockNodeType,
    getLastSelection
} from "./selection";
import {$createCustomHeadingNode, $isCustomHeadingNode} from "../nodes/custom-heading";
import {$createCustomParagraphNode, $isCustomParagraphNode} from "../nodes/custom-paragraph";
import {$createCustomQuoteNode} from "../nodes/custom-quote";
import {$createCodeBlockNode, $isCodeBlockNode, $openCodeEditorForNode, CodeBlockNode} from "../nodes/code-block";
import {$createCalloutNode, $isCalloutNode, CalloutCategory} from "../nodes/callout";

const $isHeaderNodeOfTag = (node: LexicalNode | null | undefined, tag: HeadingTagType) => {
    return $isCustomHeadingNode(node) && (node as HeadingNode).getTag() === tag;
};

export function toggleSelectionAsHeading(editor: LexicalEditor, tag: HeadingTagType) {
    editor.update(() => {
        $toggleSelectionBlockNodeType(
            (node) => $isHeaderNodeOfTag(node, tag),
            () => $createCustomHeadingNode(tag),
        )
    });
}

export function toggleSelectionAsParagraph(editor: LexicalEditor) {
    editor.update(() => {
        $toggleSelectionBlockNodeType($isCustomParagraphNode, $createCustomParagraphNode);
    });
}

export function toggleSelectionAsBlockquote(editor: LexicalEditor) {
    editor.update(() => {
        $toggleSelectionBlockNodeType($isQuoteNode, $createCustomQuoteNode);
    });
}

export function formatCodeBlock(editor: LexicalEditor) {
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        const lastSelection = getLastSelection(editor);
        const codeBlock = $getNodeFromSelection(lastSelection, $isCodeBlockNode) as (CodeBlockNode | null);
        if (codeBlock === null) {
            editor.update(() => {
                const codeBlock = $createCodeBlockNode();
                codeBlock.setCode(selection?.getTextContent() || '');
                $insertNewBlockNodeAtSelection(codeBlock, true);
                $openCodeEditorForNode(editor, codeBlock);
                codeBlock.selectStart();
            });
        } else {
            $openCodeEditorForNode(editor, codeBlock);
        }
    });
}

export function cycleSelectionCalloutFormats(editor: LexicalEditor) {
    editor.update(() => {
        const selection = $getSelection();
        const blocks = $getBlockElementNodesInSelection(selection);

        let created = false;
        for (const block of blocks) {
            if (!$isCalloutNode(block)) {
                block.replace($createCalloutNode('info'), true);
                created = true;
            }
        }

        if (created) {
            return;
        }

        const types: CalloutCategory[] = ['info', 'warning', 'danger', 'success'];
        for (const block of blocks) {
            if ($isCalloutNode(block)) {
                const type = block.getCategory();
                const typeIndex = types.indexOf(type);
                const newIndex = (typeIndex + 1) % types.length;
                const newType = types[newIndex];
                block.setCategory(newType);
            }
        }
    });
}