import {$getRoot, $getSelection, $isTextNode, LexicalEditor, LexicalNode, RootNode} from "lexical";
import {$generateHtmlFromNodes, $generateNodesFromDOM} from "@lexical/html";
import {$createCustomParagraphNode} from "./nodes/custom-paragraph";

function htmlToDom(html: string): Document {
    const parser = new DOMParser();
    return parser.parseFromString(html, 'text/html');
}

function wrapTextNodes(nodes: LexicalNode[]): LexicalNode[] {
    return nodes.map(node => {
        if ($isTextNode(node)) {
            const paragraph = $createCustomParagraphNode();
            paragraph.append(node);
            return paragraph;
        }
        return node;
    });
}

function appendNodesToRoot(root: RootNode, nodes: LexicalNode[]) {
    root.append(...wrapTextNodes(nodes));
}

export function setEditorContentFromHtml(editor: LexicalEditor, html: string) {
    const dom = htmlToDom(html);

    editor.update(() => {
        // Empty existing
        const root = $getRoot();
        for (const child of root.getChildren()) {
            child.remove(true);
        }

        const nodes = $generateNodesFromDOM(editor, dom);
        root.append(...wrapTextNodes(nodes));
    });
}

export function appendHtmlToEditor(editor: LexicalEditor, html: string) {
    const dom = htmlToDom(html);

    editor.update(() => {
        const root = $getRoot();
        const nodes = $generateNodesFromDOM(editor, dom);
        root.append(...wrapTextNodes(nodes));
    });
}

export function prependHtmlToEditor(editor: LexicalEditor, html: string) {
    const dom = htmlToDom(html);

    editor.update(() => {
        const root = $getRoot();
        const nodes = wrapTextNodes($generateNodesFromDOM(editor, dom));
        let reference = root.getChildren()[0];
        for (let i = nodes.length - 1; i >= 0; i--) {
            if (reference) {
                reference.insertBefore(nodes[i]);
            } else {
                root.append(nodes[i])
            }
            reference = nodes[i];
        }
    });
}

export function insertHtmlIntoEditor(editor: LexicalEditor, html: string) {
    const dom = htmlToDom(html);
    editor.update(() => {
        const selection = $getSelection();
        const nodes = wrapTextNodes($generateNodesFromDOM(editor, dom));

        const reference = selection?.getNodes()[0];
        const referencesParents = reference?.getParents() || [];
        const topLevel = referencesParents[referencesParents.length - 1];
        if (topLevel && reference) {
            for (let i = nodes.length - 1; i >= 0; i--) {
                reference.insertAfter(nodes[i]);
            }
        }
    });
}

export function getEditorContentAsHtml(editor: LexicalEditor): Promise<string> {
    return new Promise((resolve, reject) => {
        editor.getEditorState().read(() => {
            const html = $generateHtmlFromNodes(editor);
            resolve(html);
        });
    });
}

export function focusEditor(editor: LexicalEditor) {
    editor.focus(() => {}, {defaultSelection: "rootStart"});
}