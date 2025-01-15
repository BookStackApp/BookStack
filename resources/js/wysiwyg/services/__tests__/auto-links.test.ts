import {
    createTestContext,
    dispatchKeydownEventForNode, expectEditorStateJSONPropToEqual,
    expectNodeShapeToMatch
} from "lexical/__tests__/utils";
import {
    $getRoot,
    ParagraphNode,
    TextNode
} from "lexical";
import {registerAutoLinks} from "../auto-links";

describe('Auto-link service tests', () => {
    test('space after link in text', async () => {
        const {editor} = createTestContext();
        registerAutoLinks(editor);
        let pNode!: ParagraphNode;

        editor.updateAndCommit(() => {
            pNode = new ParagraphNode();
            const text = new TextNode('Some https://example.com?test=true text');
            pNode.append(text);
            $getRoot().append(pNode);

            text.select(34, 34);
        });

        dispatchKeydownEventForNode(pNode, editor, ' ');

        expectEditorStateJSONPropToEqual(editor, '0.1.url', 'https://example.com?test=true');
        expectEditorStateJSONPropToEqual(editor, '0.1.0.text', 'https://example.com?test=true');
    });

    test('space after link at end of line', async () => {
        const {editor} = createTestContext();
        registerAutoLinks(editor);
        let pNode!: ParagraphNode;

        editor.updateAndCommit(() => {
            pNode = new ParagraphNode();
            const text = new TextNode('Some https://example.com?test=true');
            pNode.append(text);
            $getRoot().append(pNode);

            text.selectEnd();
        });

        dispatchKeydownEventForNode(pNode, editor, ' ');

        expectNodeShapeToMatch(editor, [{type: 'paragraph', children: [
                {text: 'Some '},
                {type: 'link', children: [{text: 'https://example.com?test=true'}]}
            ]}]);
        expectEditorStateJSONPropToEqual(editor, '0.1.url', 'https://example.com?test=true');
    });

    test('enter after link in text', async () => {
        const {editor} = createTestContext();
        registerAutoLinks(editor);
        let pNode!: ParagraphNode;

        editor.updateAndCommit(() => {
            pNode = new ParagraphNode();
            const text = new TextNode('Some https://example.com?test=true text');
            pNode.append(text);
            $getRoot().append(pNode);

            text.select(34, 34);
        });

        dispatchKeydownEventForNode(pNode, editor, 'Enter');

        expectEditorStateJSONPropToEqual(editor, '0.1.url', 'https://example.com?test=true');
        expectEditorStateJSONPropToEqual(editor, '0.1.0.text', 'https://example.com?test=true');
    });
});