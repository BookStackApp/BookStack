import {HeadingNode, QuoteNode} from '@lexical/rich-text';
import {CalloutNode} from './callout';
import {
    ElementNode,
    KlassConstructor,
    LexicalNode,
    LexicalNodeReplacement, NodeMutation,
    ParagraphNode
} from "lexical";
import {CustomParagraphNode} from "./custom-paragraph";
import {LinkNode} from "@lexical/link";
import {ImageNode} from "./image";
import {DetailsNode, SummaryNode} from "./details";
import {ListItemNode, ListNode} from "@lexical/list";
import {TableCellNode, TableNode, TableRowNode} from "@lexical/table";
import {CustomTableNode} from "./custom-table";
import {HorizontalRuleNode} from "./horizontal-rule";
import {CodeBlockNode} from "./code-block";
import {DiagramNode} from "./diagram";
import {EditorUiContext} from "../ui/framework/core";
import {MediaNode} from "./media";
import {CustomListItemNode} from "./custom-list-item";
import {CustomTableCellNode} from "./custom-table-cell";
import {CustomTableRowNode} from "./custom-table-row";
import {CustomHeadingNode} from "./custom-heading";
import {CustomQuoteNode} from "./custom-quote";
import {CustomListNode} from "./custom-list";

/**
 * Load the nodes for lexical.
 */
export function getNodesForPageEditor(): (KlassConstructor<typeof LexicalNode> | LexicalNodeReplacement)[] {
    return [
        CalloutNode,
        CustomHeadingNode,
        CustomQuoteNode,
        CustomListNode,
        CustomListItemNode, // TODO - Alignment?
        CustomTableNode,
        CustomTableRowNode,
        CustomTableCellNode,
        ImageNode, // TODO - Alignment
        HorizontalRuleNode,
        DetailsNode, SummaryNode,
        CodeBlockNode,
        DiagramNode,
        MediaNode, // TODO - Alignment
        CustomParagraphNode,
        LinkNode,
        {
            replace: ParagraphNode,
            with: (node: ParagraphNode) => {
                return new CustomParagraphNode();
            }
        },
        {
            replace: HeadingNode,
            with: (node: HeadingNode) => {
                return new CustomHeadingNode(node.__tag);
            }
        },
        {
            replace: QuoteNode,
            with: (node: QuoteNode) => {
                return new CustomQuoteNode();
            }
        },
        {
            replace: ListNode,
            with: (node: ListNode) => {
                return new CustomListNode(node.getListType(), node.getStart());
            }
        },
        {
            replace: ListItemNode,
            with: (node: ListItemNode) => {
                return new CustomListItemNode(node.__value, node.__checked);
            }
        },
        {
            replace: TableNode,
            with(node: TableNode) {
                return new CustomTableNode();
            }
        },
        {
            replace: TableRowNode,
            with(node: TableRowNode) {
                return new CustomTableRowNode();
            }
        },
        {
            replace: TableCellNode,
            with: (node: TableCellNode) => {
                const cell = new CustomTableCellNode(
                    node.__headerState,
                    node.__colSpan,
                    node.__width,
                );
                cell.__rowSpan = node.__rowSpan;
                return cell;
            }
        },
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