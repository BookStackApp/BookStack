import {$getRoot, $getSelection, $insertNodes, $isBlockElementNode, LexicalEditor} from "lexical";
import {$generateHtmlFromNodes} from "@lexical/html";
import {$getNearestNodeBlockParent, $htmlToBlockNodes, $htmlToNodes} from "./nodes";

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
        const nodes = $htmlToNodes(editor, html);

        let reference = selection?.getNodes()[0];
        let replacedReference = false;
        let parentBlock = reference ? $getNearestNodeBlockParent(reference) : null;

        for (let i = nodes.length - 1; i >= 0; i--) {
            const toInsert = nodes[i];
            if ($isBlockElementNode(toInsert) && parentBlock) {
                // Insert at a block level, before or after the referenced block
                // depending on if the reference has been replaced.
                if (replacedReference) {
                    parentBlock.insertBefore(toInsert);
                } else {
                    parentBlock.insertAfter(toInsert);
                }
            } else if ($isBlockElementNode(toInsert)) {
                // Otherwise append blocks to the root
                $getRoot().append(toInsert);
            } else if (!replacedReference) {
                // First inline node, replacing existing selection
                $insertNodes([toInsert]);
                reference = toInsert;
                parentBlock = $getNearestNodeBlockParent(reference);
                replacedReference = true;
            } else {
                // For other inline nodes, insert before the reference node
                reference?.insertBefore(toInsert)
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