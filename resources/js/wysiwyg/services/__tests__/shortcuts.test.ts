import {
    createTestContext, destroyFromContext,
    dispatchKeydownEventForSelectedNode, expectEditorStateJSONPropToEqual,
} from "lexical/__tests__/utils";
import {
    $createParagraphNode, $createTextNode,
    $getRoot, IS_BOLD, LexicalEditor,
} from "lexical";
import {registerRichText} from "@lexical/rich-text";
import {EditorUiContext} from "../../ui/framework/core";
import {registerShortcuts} from "../shortcuts";

describe('Keyboard-handling service tests', () => {

    let context!: EditorUiContext;
    let editor!: LexicalEditor;

    beforeEach(() => {
        context = createTestContext();
        editor = context.editor;
        registerRichText(editor);
        registerShortcuts(context, true);
    });

    afterEach(() => {
        destroyFromContext(context);
    });

    test('Basic block format shortcuts works', () => {
        editor.updateAndCommit(() => {
            const p = $createParagraphNode();
            p.append($createTextNode('Hello World'))
            $getRoot().append(p);
            p.select();
        });

        dispatchKeydownEventForSelectedNode(editor, '1', {ctrlKey: true});

        expectEditorStateJSONPropToEqual(editor, '0.type', 'heading');
        expectEditorStateJSONPropToEqual(editor, '0.tag', 'h2');

        dispatchKeydownEventForSelectedNode(editor, '2', {ctrlKey: true});

        expectEditorStateJSONPropToEqual(editor, '0.type', 'heading');
        expectEditorStateJSONPropToEqual(editor, '0.tag', 'h3');

        dispatchKeydownEventForSelectedNode(editor, 'd', {ctrlKey: true});

        expectEditorStateJSONPropToEqual(editor, '0.type', 'paragraph');
        expectEditorStateJSONPropToEqual(editor, '0.0.text', 'Hello World');
    });

    test('Basic bold format shortcut works', () => {
        editor.updateAndCommit(() => {
            const p = $createParagraphNode();
            const text = $createTextNode('Hello World');
            p.append(text)
            $getRoot().append(p);
            text.select(0, 5);
        });

        // Toggle bold for selection
        dispatchKeydownEventForSelectedNode(editor, 'b', {ctrlKey: true});
        expectEditorStateJSONPropToEqual(editor, '0.0.format', IS_BOLD);
        expectEditorStateJSONPropToEqual(editor, '0.1.format', 0);

        // Untoggle bold for selection
        dispatchKeydownEventForSelectedNode(editor, 'b', {ctrlKey: true});
        expectEditorStateJSONPropToEqual(editor, '0.0.format', 0);
    });

    test('Basic bold format shortcut works when using cyrillic equivalent keys', () => {
        editor.updateAndCommit(() => {
            const p = $createParagraphNode();
            const text = $createTextNode('Hello World');
            p.append(text)
            $getRoot().append(p);
            text.select(0, 5);
        });

        // Toggle bold for selection
        dispatchKeydownEventForSelectedNode(editor, 'и', {ctrlKey: true, keyCode: 66});
        expectEditorStateJSONPropToEqual(editor, '0.0.format', IS_BOLD);
        expectEditorStateJSONPropToEqual(editor, '0.1.format', 0);

        // Untoggle bold for selection
        dispatchKeydownEventForSelectedNode(editor, 'и', {ctrlKey: true, keyCode: 66});
        expectEditorStateJSONPropToEqual(editor, '0.0.format', 0);
    });

});
