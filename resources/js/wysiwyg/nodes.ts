import {CalloutNode} from '@lexical/rich-text/LexicalCalloutNode';
import {
    ElementNode,
    KlassConstructor,
    LexicalNode,
    LexicalNodeReplacement, NodeMutation,
    ParagraphNode
} from "lexical";
import {LinkNode} from "@lexical/link";
import {ImageNode} from "@lexical/rich-text/LexicalImageNode";
import {DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {ListItemNode, ListNode} from "@lexical/list";
import {TableCellNode, TableNode, TableRowNode} from "@lexical/table";
import {HorizontalRuleNode} from "@lexical/rich-text/LexicalHorizontalRuleNode";
import {CodeBlockNode} from "@lexical/rich-text/LexicalCodeBlockNode";
import {DiagramNode} from "@lexical/rich-text/LexicalDiagramNode";
import {EditorUiContext} from "./ui/framework/core";
import {MediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {HeadingNode} from "@lexical/rich-text/LexicalHeadingNode";
import {QuoteNode} from "@lexical/rich-text/LexicalQuoteNode";
import {CaptionNode} from "@lexical/table/LexicalCaptionNode";

export function getNodesForPageEditor(): (KlassConstructor<typeof LexicalNode> | LexicalNodeReplacement)[] {
    return [
        CalloutNode,
        HeadingNode,
        QuoteNode,
        ListNode,
        ListItemNode,
        TableNode,
        TableRowNode,
        TableCellNode,
        CaptionNode,
        ImageNode, // TODO - Alignment
        HorizontalRuleNode,
        DetailsNode,
        CodeBlockNode,
        DiagramNode,
        MediaNode, // TODO - Alignment
        ParagraphNode,
        LinkNode,
    ];
}

export function getNodesForBasicEditor(): (KlassConstructor<typeof LexicalNode> | LexicalNodeReplacement)[] {
    return [
        ListNode,
        ListItemNode,
        ParagraphNode,
        LinkNode,
    ];
}

export function registerCommonNodeMutationListeners(context: EditorUiContext): void {
    const decorated = [ImageNode, CodeBlockNode, DiagramNode];

    const decorationDestroyListener = (mutations: Map<string, NodeMutation>): void => {
        for (let [nodeKey, mutation] of mutations) {
            if (mutation === "destroyed") {
                const decorator = context.manager.getDecoratorByNodeKey(nodeKey);
                if (decorator) {
                    decorator.teardown();
                }
            }
        }
    };

    for (let decoratedNode of decorated) {
        // Have to pass a unique function here since they are stored by lexical keyed on listener function.
        context.editor.registerMutationListener(decoratedNode, (mutations) => decorationDestroyListener(mutations));
    }
}

export type LexicalNodeMatcher = (node: LexicalNode|null|undefined) => boolean;
export type LexicalElementNodeCreator = () => ElementNode;