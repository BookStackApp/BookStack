import {DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";

export function $unwrapDetailsNode(node: DetailsNode) {
    const children = node.getChildren();
    for (const child of children) {
        node.insertBefore(child);
    }
    node.remove();
}