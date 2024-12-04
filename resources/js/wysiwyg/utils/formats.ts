import {
    $createParagraphNode,
    $createTextNode,
    $getSelection,
    $insertNodes,
    $isParagraphNode,
    LexicalEditor,
    LexicalNode
} from "lexical";
import {
    $getBlockElementNodesInSelection,
    $getNodeFromSelection,
    $insertNewBlockNodeAtSelection, $selectionContainsNodeType, $selectSingleNode,
    $toggleSelectionBlockNodeType,
    getLastSelection
} from "./selection";
import {$createCodeBlockNode, $isCodeBlockNode, $openCodeEditorForNode, CodeBlockNode} from "@lexical/rich-text/LexicalCodeBlockNode";
import {$createCalloutNode, $isCalloutNode, CalloutCategory} from "@lexical/rich-text/LexicalCalloutNode";
import {$isListNode, insertList, ListNode, ListType, removeList} from "@lexical/list";
import {$createLinkNode, $isLinkNode} from "@lexical/link";
import {$createHeadingNode, $isHeadingNode, HeadingTagType} from "@lexical/rich-text/LexicalHeadingNode";
import {$createQuoteNode, $isQuoteNode} from "@lexical/rich-text/LexicalQuoteNode";

const $isHeaderNodeOfTag = (node: LexicalNode | null | undefined, tag: HeadingTagType) => {
    return $isHeadingNode(node) && node.getTag() === tag;
};

export function toggleSelectionAsHeading(editor: LexicalEditor, tag: HeadingTagType) {
    editor.update(() => {
        $toggleSelectionBlockNodeType(
            (node) => $isHeaderNodeOfTag(node, tag),
            () => $createHeadingNode(tag),
        )
    });
}

export function toggleSelectionAsParagraph(editor: LexicalEditor) {
    editor.update(() => {
        $toggleSelectionBlockNodeType($isParagraphNode, $createParagraphNode);
    });
}

export function toggleSelectionAsBlockquote(editor: LexicalEditor) {
    editor.update(() => {
        $toggleSelectionBlockNodeType($isQuoteNode, $createQuoteNode);
    });
}

export function toggleSelectionAsList(editor: LexicalEditor, type: ListType) {
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        const listSelected = $selectionContainsNodeType(selection, (node: LexicalNode | null | undefined): boolean => {
            return $isListNode(node) && (node as ListNode).getListType() === type;
        });

        if (listSelected) {
            removeList(editor);
        } else {
            insertList(editor, type);
        }
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

                const selectionNodes = $getBlockElementNodesInSelection(selection);
                const firstSelectionNode = selectionNodes[0];
                const extraNodes = selectionNodes.slice(1);
                if (firstSelectionNode) {
                    firstSelectionNode.replace(codeBlock);
                    extraNodes.forEach(n => n.remove());
                } else {
                    $insertNewBlockNodeAtSelection(codeBlock, true);
                }

                $openCodeEditorForNode(editor, codeBlock);
                $selectSingleNode(codeBlock);
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

export function insertOrUpdateLink(editor: LexicalEditor, linkDetails: {text: string, title: string, target: string, url: string}) {
    editor.update(() => {
        const selection = $getSelection();
        let link = $getNodeFromSelection(selection, $isLinkNode);
        if ($isLinkNode(link)) {
            link.setURL(linkDetails.url);
            link.setTarget(linkDetails.target);
            link.setTitle(linkDetails.title);
        } else {
            link = $createLinkNode(linkDetails.url, {
                title: linkDetails.title,
                target: linkDetails.target,
            });

            $insertNodes([link]);
        }

        if ($isLinkNode(link)) {
            for (const child of link.getChildren()) {
                child.remove(true);
            }
            link.append($createTextNode(linkDetails.text));
        }
    });
}