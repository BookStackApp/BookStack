import {createTestContext} from "lexical/__tests__/utils";
import {$createMediaNode} from "@lexical/rich-text/LexicalMediaNode";


describe('LexicalMediaNode', () => {

    test('setWidth/setHeight/setWidthAndHeight functions remove relevant styles', () => {
        const {editor} = createTestContext();
        editor.updateAndCommit(() => {
            const mediaMode = $createMediaNode('video');
            const defaultStyles = {style: 'width:20px;height:40px;color:red'};

            mediaMode.setAttributes(defaultStyles);
            mediaMode.setWidth(60);
            expect(mediaMode.getWidth()).toBe(60);
            expect(mediaMode.getAttributes().style).toBe('height:40px;color:red');

            mediaMode.setAttributes(defaultStyles);
            mediaMode.setHeight(77);
            expect(mediaMode.getHeight()).toBe(77);
            expect(mediaMode.getAttributes().style).toBe('width:20px;color:red');

            mediaMode.setAttributes(defaultStyles);
            mediaMode.setWidthAndHeight('6', '7');
            expect(mediaMode.getWidth()).toBe(6);
            expect(mediaMode.getHeight()).toBe(7);
            expect(mediaMode.getAttributes().style).toBe('color:red');
        });
    });

});