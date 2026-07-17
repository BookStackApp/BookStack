import {createTestContext} from "lexical/__tests__/utils";
import {$createDiagramNode, DiagramNode} from "@lexical/rich-text/LexicalDiagramNode";
import {$getHtmlContent} from "@lexical/clipboard";
import {getEditorContentAsHtml} from "../../../../utils/actions";
import {$getRoot} from "lexical";


describe('LexicalDiagramNode', () => {

    test('clone creates new instance with same key', () => {
        const {editor} = createTestContext();
        editor.updateAndCommit(() => {
            const node = $createDiagramNode('10', 'https://example.com/barry.png');
            const clone = DiagramNode.clone(node);

            expect(node).not.toBe(clone);
            expect(node.getKey()).toBe(clone.getKey());
        });
    });

    test('output HTML format', async () => {
        const {editor} = createTestContext();
        editor.updateAndCommit(() => {
            const node = $createDiagramNode('10', 'https://example.com/barry.png');
            node.setId('cat-123');
            $getRoot().append(node);
        });

        const html = await getEditorContentAsHtml(editor);
        expect(html).toBe(`<div id="cat-123" drawio-diagram="10"><img src="https://example.com/barry.png"></div>`);
    });

});