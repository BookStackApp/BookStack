import {HeadingNode, QuoteNode} from '@lexical/rich-text';
import {Callout} from './callout';
import {KlassConstructor, LexicalNode} from "lexical";

/**
 * Load the nodes for lexical.
 */
export function getNodesForPageEditor(): KlassConstructor<typeof LexicalNode>[] {
    return [
        Callout,
        HeadingNode,
        QuoteNode,
    ];
}
