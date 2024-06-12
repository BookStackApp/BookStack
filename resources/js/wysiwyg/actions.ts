import {$getRoot, LexicalEditor} from "lexical";
import {$generateHtmlFromNodes, $generateNodesFromDOM} from "@lexical/html";


export function setEditorContentFromHtml(editor: LexicalEditor, html: string) {
    const parser = new DOMParser();
    const dom = parser.parseFromString(html, 'text/html');

    editor.update(() => {
        const nodes = $generateNodesFromDOM(editor, dom);
        const root = $getRoot();
        for (const child of root.getChildren()) {
            child.remove(true);
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