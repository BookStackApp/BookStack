import {HeadingNode, QuoteNode} from '@lexical/rich-text';
import {CalloutNode} from './callout';
import {ElementNode, KlassConstructor, LexicalNode, LexicalNodeReplacement, ParagraphNode} from "lexical";
import {CustomParagraphNode} from "./custom-paragraph";
import {LinkNode} from "@lexical/link";
import {ImageNode} from "./image";
import {DetailsNode, SummaryNode} from "./details";

/**
 * Load the nodes for lexical.
 */
export function getNodesForPageEditor(): (KlassConstructor<typeof LexicalNode> | LexicalNodeReplacement)[] {
    return [
        CalloutNode, // Todo - Create custom
        HeadingNode, // Todo - Create custom
        QuoteNode, // Todo - Create custom
        ImageNode,
        DetailsNode, SummaryNode,
        CustomParagraphNode,
        {
            replace: ParagraphNode,
            with: (node: ParagraphNode) => {
                return new CustomParagraphNode();
            }
        },
        LinkNode,
    ];
}

export type LexicalNodeMatcher = (node: LexicalNode|null|undefined) => boolean;
export type LexicalElementNodeCreator = () => ElementNode;