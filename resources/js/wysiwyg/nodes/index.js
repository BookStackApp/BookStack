import {HeadingNode, QuoteNode} from '@lexical/rich-text';
import {Callout} from './callout';

/**
 * Load the nodes for lexical.
 * @returns {LexicalNode[]}
 */
export function getNodesForPageEditor() {
    return [
        Callout,
        HeadingNode,
        QuoteNode,
    ];
}
