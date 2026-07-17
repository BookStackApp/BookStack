import {
    createTestContext,
    destroyFromContext,
    expectNodeShapeToMatch,
} from "lexical/__tests__/utils";
import { $createParagraphNode, $createTextNode, $getRoot, LexicalEditor } from "lexical";
import { EditorUiContext } from "../../ui/framework/core";
import { setEditorContentFromHtml } from "../actions";

describe('Actions', () => {

    let context!: EditorUiContext;
    let editor!: LexicalEditor;

    beforeEach(() => {
        context = createTestContext();
        editor = context.editor;
    });

    afterEach(() => {
        destroyFromContext(context);
    });

    describe('setEditorContentFromHtml', () => {
        it('parses HTML and sets content in editor', async () => {
            setEditorContentFromHtml(editor, '<p>Hello World</p>');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                {
                    type: 'paragraph',
                    children: [{ text: 'Hello World' }],
                },
            ]);
        });

        it('parses multiple block elements', async () => {
            setEditorContentFromHtml(editor, '<p>First</p><p>Second</p>');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                { type: 'paragraph', children: [{ text: 'First' }] },
                { type: 'paragraph', children: [{ text: 'Second' }] },
            ]);
        });

        it('wraps plain text in a paragraph', async () => {
            setEditorContentFromHtml(editor, 'Plain text');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                {
                    type: 'paragraph',
                    children: [{ text: 'Plain text' }],
                },
            ]);
        });

        it('ensures at least a paragraph when HTML is empty', async () => {
            setEditorContentFromHtml(editor, '');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                { type: 'paragraph' },
            ]);
        });

        it('ensures at least a paragraph when HTML contains only whitespace', async () => {
            setEditorContentFromHtml(editor, '   ');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                { type: 'paragraph' },
            ]);
        });

        it('clears existing content before setting new content', async () => {
            editor.updateAndCommit(() => {
                const p = $createParagraphNode();
                p.append($createTextNode('Existing'));
                $getRoot().append(p);
            });

            setEditorContentFromHtml(editor, '<p>New</p>');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                { type: 'paragraph', children: [{ text: 'New' }] },
            ]);
        });

        it('handles nested HTML structures', async () => {
            setEditorContentFromHtml(editor, '<ul><li>Item A</li><li>Item B</li></ul>');
            await Promise.resolve().then();

            expectNodeShapeToMatch(editor, [
                {
                    type: 'list',
                    children: [
                        { type: 'listitem', children: [{ text: 'Item A' }] },
                        { type: 'listitem', children: [{ text: 'Item B' }] },
                    ],
                },
            ]);
        });
    });
});
