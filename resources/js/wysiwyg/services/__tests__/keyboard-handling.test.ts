import {
    createTestContext, destroyFromContext,
    dispatchKeydownEventForNode,
    dispatchKeydownEventForSelectedNode, expectNodeShapeToMatch,
} from "lexical/__tests__/utils";
import {
    $createParagraphNode, $createTextNode,
    $getRoot, $getSelection, LexicalEditor, LexicalNode,
    ParagraphNode, TextNode,
} from "lexical";
import {$createDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {registerKeyboardHandling} from "../keyboard-handling";
import {registerRichText} from "@lexical/rich-text";
import {EditorUiContext} from "../../ui/framework/core";
import {$createListItemNode, $createListNode, ListItemNode, ListNode} from "@lexical/list";
import {$createImageNode, ImageNode} from "@lexical/rich-text/LexicalImageNode";

describe('Keyboard-handling service tests', () => {

    let context!: EditorUiContext;
    let editor!: LexicalEditor;

    beforeEach(() => {
        context = createTestContext();
        editor = context.editor;
        registerRichText(editor);
        registerKeyboardHandling(context);
    });

    afterEach(() => {
        destroyFromContext(context);
    });

    test('Details: down key on last lines creates new sibling node', () => {
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

        editor.getEditorState().read(() => {
            lastRootChild = $getRoot().getLastChild();
        });

        expect(lastRootChild).toBeInstanceOf(ParagraphNode);
    });

    test('Details: enter on last empty block creates new sibling node', () => {
        registerRichText(editor);

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
        dispatchKeydownEventForSelectedNode(editor, 'Enter');

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

    test('Lists: tab on empty list item insets item', () => {

        let list!: ListNode;
        let listItemB!: ListItemNode;

        editor.updateAndCommit(() => {
            const root = $getRoot();
            list = $createListNode('bullet');
            const listItemA = $createListItemNode();
            listItemA.append($createTextNode('Hello!'));
            listItemB = $createListItemNode();
            list.append(listItemA, listItemB);
            root.append(list);
            listItemB.selectStart();
        });

        dispatchKeydownEventForNode(listItemB, editor, 'Tab');

        editor.getEditorState().read(() => {
            const list = $getRoot().getChildren()[0] as ListNode;
            const listChild = list.getChildren()[0] as ListItemNode;
            const children = listChild.getChildren();
            expect(children).toHaveLength(2);
            expect(children[0]).toBeInstanceOf(TextNode);
            expect(children[0].getTextContent()).toBe('Hello!');
            expect(children[1]).toBeInstanceOf(ListNode);

            const innerList = children[1] as ListNode;
            const selectedNode = $getSelection()?.getNodes()[0];
            expect(selectedNode).toBeInstanceOf(ListItemNode);
            expect(selectedNode?.getKey()).toBe(innerList.getChildren()[0].getKey());
        });
    });

    test('Images: up on selected image creates new paragraph if none above', () => {
        let image!: ImageNode;
        editor.updateAndCommit(() => {
            const root = $getRoot();
            const imageWrap = $createParagraphNode();
            image = $createImageNode('https://example.com/cat.png');
            imageWrap.append(image);
            root.append(imageWrap);
            image.select();
        });

        expectNodeShapeToMatch(editor, [{
            type: 'paragraph',
            children: [
                {type: 'image'}
            ],
        }]);

        dispatchKeydownEventForNode(image, editor, 'ArrowUp');

        expectNodeShapeToMatch(editor, [{
            type: 'paragraph',
        }, {
            type: 'paragraph',
            children: [
                {type: 'image'}
            ],
        }]);
    });
});