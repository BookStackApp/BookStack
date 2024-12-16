import {
    createTestContext,
    dispatchKeydownEventForNode,
    dispatchKeydownEventForSelectedNode,
    initializeUnitTest
} from "lexical/__tests__/utils";
import {
    $createParagraphNode, $createTextNode,
    $getRoot, LexicalNode,
    ParagraphNode,
} from "lexical";
import {$createDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {registerKeyboardHandling} from "../keyboard-handling";
import {registerRichText} from "@lexical/rich-text";

describe('Keyboard-handling service tests', () => {
    initializeUnitTest((testEnv) => {

        test('Details: down key on last lines creates new sibling node', () => {
            const {editor} = testEnv;

            registerRichText(editor);
            registerKeyboardHandling(createTestContext(testEnv));

            let lastRootChild!: LexicalNode|null;
            let detailsPara!: ParagraphNode;

            editor.updateAndCommit(() => {
                const root = $getRoot()
                const details = $createDetailsNode();
                detailsPara = $createParagraphNode();
                details.append(detailsPara);
                $getRoot().append(details);
                detailsPara.select();

                lastRootChild = root.getLastChild();
            });

            expect(lastRootChild).toBeInstanceOf(DetailsNode);

            dispatchKeydownEventForNode(detailsPara, editor, 'ArrowDown');
            editor.commitUpdates();

            editor.getEditorState().read(() => {
                lastRootChild = $getRoot().getLastChild();
            });

            expect(lastRootChild).toBeInstanceOf(ParagraphNode);
        });

        test('Details: enter on last empy block creates new sibling node', () => {
            const {editor} = testEnv;

            registerRichText(editor);
            registerKeyboardHandling(createTestContext(testEnv));

            let lastRootChild!: LexicalNode|null;
            let detailsPara!: ParagraphNode;

            editor.updateAndCommit(() => {
                const root = $getRoot()
                const details = $createDetailsNode();
                const text = $createTextNode('Hello!');
                detailsPara = $createParagraphNode();
                detailsPara.append(text);
                details.append(detailsPara);
                $getRoot().append(details);
                text.selectEnd();

                lastRootChild = root.getLastChild();
            });

            expect(lastRootChild).toBeInstanceOf(DetailsNode);

            dispatchKeydownEventForNode(detailsPara, editor, 'Enter');
            editor.commitUpdates();

            dispatchKeydownEventForSelectedNode(editor, 'Enter');
            editor.commitUpdates();

            let detailsChildren!: LexicalNode[];
            let lastDetailsText!: string;

            editor.getEditorState().read(() => {
                detailsChildren = (lastRootChild as DetailsNode).getChildren();
                lastRootChild = $getRoot().getLastChild();
                lastDetailsText = detailsChildren[0].getTextContent();
            });

            expect(lastRootChild).toBeInstanceOf(ParagraphNode);
            expect(detailsChildren).toHaveLength(1);
            expect(lastDetailsText).toBe('Hello!');
        });
    });
});