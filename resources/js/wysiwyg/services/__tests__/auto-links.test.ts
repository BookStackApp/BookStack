import {initializeUnitTest} from "lexical/__tests__/utils";
import {SerializedLinkNode} from "@lexical/link";
import {
    $getRoot,
    ParagraphNode,
    SerializedParagraphNode,
    SerializedTextNode,
    TextNode
} from "lexical";
import {registerAutoLinks} from "../auto-links";

describe('Auto-link service tests', () => {
    initializeUnitTest((testEnv) => {

        test('space after link in text', async () => {
            const {editor} = testEnv;

            registerAutoLinks(editor);
            let pNode!: ParagraphNode;

            editor.update(() => {
                pNode = new ParagraphNode();
                const text = new TextNode('Some https://example.com?test=true text');
                pNode.append(text);
                $getRoot().append(pNode);

                text.select(34, 34);
            });

            editor.commitUpdates();

            const pDomEl = editor.getElementByKey(pNode.getKey());
            const event = new KeyboardEvent('keydown', {
                bubbles: true,
                cancelable: true,
                key: ' ',
                keyCode: 62,
            });
            pDomEl?.dispatchEvent(event);

            editor.commitUpdates();

            const paragraph = editor!.getEditorState().toJSON().root
                .children[0] as SerializedParagraphNode;
            expect(paragraph.children[1].type).toBe('link');

            const link = paragraph.children[1] as SerializedLinkNode;
            expect(link.url).toBe('https://example.com?test=true');
            const linkText = link.children[0] as SerializedTextNode;
            expect(linkText.text).toBe('https://example.com?test=true');
        });

        test('enter after link in text', async () => {
            const {editor} = testEnv;

            registerAutoLinks(editor);
            let pNode!: ParagraphNode;

            editor.update(() => {
                pNode = new ParagraphNode();
                const text = new TextNode('Some https://example.com?test=true text');
                pNode.append(text);
                $getRoot().append(pNode);

                text.select(34, 34);
            });

            editor.commitUpdates();

            const pDomEl = editor.getElementByKey(pNode.getKey());
            const event = new KeyboardEvent('keydown', {
                bubbles: true,
                cancelable: true,
                key: 'Enter',
                keyCode: 66,
            });
            pDomEl?.dispatchEvent(event);

            editor.commitUpdates();

            const paragraph = editor!.getEditorState().toJSON().root
                .children[0] as SerializedParagraphNode;
            expect(paragraph.children[1].type).toBe('link');

            const link = paragraph.children[1] as SerializedLinkNode;
            expect(link.url).toBe('https://example.com?test=true');
            const linkText = link.children[0] as SerializedTextNode;
            expect(linkText.text).toBe('https://example.com?test=true');
        });
    });
});