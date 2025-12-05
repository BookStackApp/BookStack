import {createEditorApiInstance} from "./api-test-utils";
import {$createParagraphNode, $createTextNode, $getRoot, IS_BOLD, LexicalEditor} from "lexical";
import {expectNodeShapeToMatch} from "lexical/__tests__/utils";


describe('Editor API: Content Module', () => {

    describe('insertHtml()', () => {
        it('should insert html at selection by default', () => {
            const {api, editor} = createEditorApiInstance();
            insertAndSelectSampleBlock(editor);

            api.content.insertHtml('<strong>pp</strong>');
            editor.commitUpdates();

            expectNodeShapeToMatch(editor, [
                {type: 'paragraph', children: [
                    {text: 'He'},
                    {text: 'pp', format: IS_BOLD},
                    {text: 'o World'}
                ]}
            ]);
        });

        it('should handle a mix of inline and block elements', () => {
            const {api, editor} = createEditorApiInstance();
            insertAndSelectSampleBlock(editor);

            api.content.insertHtml('<p>cat</p><strong>pp</strong><p>dog</p>');
            editor.commitUpdates();

            expectNodeShapeToMatch(editor, [
                {type: 'paragraph', children: [{text: 'cat'}]},
                {type: 'paragraph', children: [
                        {text: 'He'},
                        {text: 'pp', format: IS_BOLD},
                        {text: 'o World'}
                    ]},
                {type: 'paragraph', children: [{text: 'dog'}]},
            ]);
        });

        it('should throw and error if an invalid position is provided', () => {
            const {api, editor} = createEditorApiInstance();
            insertAndSelectSampleBlock(editor);


            expect(() => {
                api.content.insertHtml('happy<p>cat</p>', 'near-the-end');
            }).toThrow('Invalid position: near-the-end. Valid positions are: start, end, selection');
        });

        it('should append html if end provided as a position', () => {
            const {api, editor} = createEditorApiInstance();
            insertAndSelectSampleBlock(editor);

            api.content.insertHtml('happy<p>cat</p>', 'end');
            editor.commitUpdates();

            expectNodeShapeToMatch(editor, [
                {type: 'paragraph', children: [{text: 'Hello World'}]},
                {type: 'paragraph', children: [{text: 'happy'}]},
                {type: 'paragraph', children: [{text: 'cat'}]},
            ]);
        });

        it('should prepend html if start provided as a position', () => {
            const {api, editor} = createEditorApiInstance();
            insertAndSelectSampleBlock(editor);

            api.content.insertHtml('happy<p>cat</p>', 'start');
            editor.commitUpdates();

            expectNodeShapeToMatch(editor, [
                {type: 'paragraph', children: [{text: 'happy'}]},
                {type: 'paragraph', children: [{text: 'cat'}]},
                {type: 'paragraph', children: [{text: 'Hello World'}]},
            ]);
        });
    });

    function insertAndSelectSampleBlock(editor: LexicalEditor) {
        editor.updateAndCommit(() => {
            const p = $createParagraphNode();
            const text = $createTextNode('Hello World');
            p.append(text);
            $getRoot().append(p);

            text.select(2, 4);
        });
    }

});