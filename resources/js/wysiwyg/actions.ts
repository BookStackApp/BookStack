import {$createParagraphNode, $getRoot, $isTextNode, LexicalEditor} from "lexical";
import {$generateHtmlFromNodes, $generateNodesFromDOM} from "@lexical/html";
import {$createCustomParagraphNode} from "./nodes/custom-paragraph";


export function setEditorContentFromHtml(editor: LexicalEditor, html: string) {
    const parser = new DOMParser();
    const dom = parser.parseFromString(html, 'text/html');

    console.log(html);
    editor.update(() => {
        // Empty existing
        const root = $getRoot();
        for (const child of root.getChildren()) {
            child.remove(true);
        }

        const nodes = $generateNodesFromDOM(editor, dom);

        // Wrap top-level text nodes
        for (let i = 0; i < nodes.length; i++) {
            const node = nodes[i];
            if ($isTextNode(node)) {
                const paragraph = $createCustomParagraphNode();
                paragraph.append(node);
                nodes[i] = paragraph;
            }
        }

        root.append(...nodes);
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