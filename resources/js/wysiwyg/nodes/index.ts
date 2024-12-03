import {CalloutNode} from './callout';
import {
    ElementNode,
    KlassConstructor,
    LexicalNode,
    LexicalNodeReplacement, NodeMutation,
    ParagraphNode
} from "lexical";
import {LinkNode} from "@lexical/link";
import {ImageNode} from "./image";
import {DetailsNode, SummaryNode} from "./details";
import {ListItemNode, ListNode} from "@lexical/list";
import {TableCellNode, TableNode, TableRowNode} from "@lexical/table";
import {HorizontalRuleNode} from "./horizontal-rule";
import {CodeBlockNode} from "./code-block";
import {DiagramNode} from "./diagram";
import {EditorUiContext} from "../ui/framework/core";
import {MediaNode} from "./media";
import {HeadingNode} from "@lexical/rich-text/LexicalHeadingNode";
import {QuoteNode} from "@lexical/rich-text/LexicalQuoteNode";

/**
 * Load the nodes for lexical.
 */
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
        ImageNode, // TODO - Alignment
        HorizontalRuleNode,
        DetailsNode, SummaryNode,
        CodeBlockNode,
        DiagramNode,
        MediaNode, // TODO - Alignment
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
                    decorator.destroy(context);
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