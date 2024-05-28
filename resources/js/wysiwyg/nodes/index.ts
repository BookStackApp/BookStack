import {HeadingNode, QuoteNode} from '@lexical/rich-text';
import {CalloutNode} from './callout';
import {KlassConstructor, LexicalNode, LexicalNodeReplacement, ParagraphNode} from "lexical";
import {CustomParagraphNode} from "./custom-paragraph";

/**
 * Load the nodes for lexical.
 */
export function getNodesForPageEditor(): (KlassConstructor<typeof LexicalNode> | LexicalNodeReplacement)[] {
    return [
        CalloutNode, // Todo - Create custom
        HeadingNode, // Todo - Create custom
        QuoteNode, // Todo - Create custom
        CustomParagraphNode,
        {
            replace: ParagraphNode,
            with: (node: ParagraphNode) => {
                return new CustomParagraphNode();
            }
        }
    ];
}
