import {HeadingNode, QuoteNode} from '@lexical/rich-text';
import {CalloutNode} from './callout';
import {ElementNode, KlassConstructor, LexicalNode, LexicalNodeReplacement, ParagraphNode} from "lexical";
import {CustomParagraphNode} from "./custom-paragraph";
import {LinkNode} from "@lexical/link";
import {ImageNode} from "./image";
import {DetailsNode, SummaryNode} from "./details";
import {ListItemNode, ListNode} from "@lexical/list";
import {TableCellNode, TableNode, TableRowNode} from "@lexical/table";
import {CustomTableNode} from "./custom-table";

/**
 * Load the nodes for lexical.
 */
export function getNodesForPageEditor(): (KlassConstructor<typeof LexicalNode> | LexicalNodeReplacement)[] {
    return [
        CalloutNode, // Todo - Create custom
        HeadingNode, // Todo - Create custom
        QuoteNode, // Todo - Create custom
        ListNode, // Todo - Create custom
        ListItemNode,
        CustomTableNode,
        TableRowNode,
        TableCellNode,
        ImageNode,
        DetailsNode, SummaryNode,
        CustomParagraphNode,
        LinkNode,
        {
            replace: ParagraphNode,
            with: (node: ParagraphNode) => {
                return new CustomParagraphNode();
            }
        },
        {
            replace: TableNode,
            with(node: TableNode) {
                return new CustomTableNode();
            }
        },
    ];
}

export type LexicalNodeMatcher = (node: LexicalNode|null|undefined) => boolean;
export type LexicalElementNodeCreator = () => ElementNode;