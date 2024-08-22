import {$isElementNode, LexicalEditor, LexicalNode, SerializedLexicalNode} from "lexical";

type SerializedLexicalNodeWithChildren = {
    node: SerializedLexicalNode,
    children: SerializedLexicalNodeWithChildren[],
};

function serializeNodeRecursive(node: LexicalNode): SerializedLexicalNodeWithChildren {
    const childNodes = $isElementNode(node) ? node.getChildren() : [];
    return {
        node: node.exportJSON(),
        children: childNodes.map(n => serializeNodeRecursive(n)),
    };
}

function unserializeNodeRecursive(editor: LexicalEditor, {node, children}: SerializedLexicalNodeWithChildren): LexicalNode|null {
    const instance = editor._nodes.get(node.type)?.klass.importJSON(node);
    if (!instance) {
        return null;
    }

    const childNodes = children.map(child => unserializeNodeRecursive(editor, child));
    for (const child of childNodes) {
        if (child && $isElementNode(instance)) {
            instance.append(child);
        }
    }

    return instance;
}

export class NodeClipboard<T extends LexicalNode> {
    protected store: SerializedLexicalNodeWithChildren[] = [];

    set(...nodes: LexicalNode[]): void {
        this.store.splice(0, this.store.length);
        for (const node of nodes) {
            this.store.push(serializeNodeRecursive(node));
        }
    }

    get(editor: LexicalEditor): T[] {
        return this.store.map(json => unserializeNodeRecursive(editor, json)).filter((node) => {
            return node !== null;
        }) as T[];
    }

    size(): number {
        return this.store.length;
    }
}