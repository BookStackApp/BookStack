import {$getRoot, $getSelection, LexicalEditor} from "lexical";
import {$generateHtmlFromNodes} from "@lexical/html";
import {$htmlToBlockNodes} from "./nodes";

export function setEditorContentFromHtml(editor: LexicalEditor, html: string) {
    editor.update(() => {
        // Empty existing
        const root = $getRoot();
        for (const child of root.getChildren()) {
            child.remove(true);
        }

        const nodes = $htmlToBlockNodes(editor, html);
        root.append(...nodes);
    });
}

export function appendHtmlToEditor(editor: LexicalEditor, html: string) {
    editor.update(() => {
        const root = $getRoot();
        const nodes = $htmlToBlockNodes(editor, html);
        root.append(...nodes);
    });
}

export function prependHtmlToEditor(editor: LexicalEditor, html: string) {
    editor.update(() => {
        const root = $getRoot();
        const nodes = $htmlToBlockNodes(editor, html);
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
    editor.update(() => {
        const selection = $getSelection();
        const nodes = $htmlToBlockNodes(editor, html);

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

export function focusEditor(editor: LexicalEditor): void {
    editor.focus(() => {}, {defaultSelection: "rootStart"});
}