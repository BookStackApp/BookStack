import {dispatchKeydownEventForNode, initializeUnitTest} from "lexical/__tests__/utils";
import {$createDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {$createParagraphNode, $getRoot, LexicalNode, ParagraphNode} from "lexical";

const editorConfig = Object.freeze({
    namespace: '',
    theme: {
    },
});

describe('LexicalDetailsNode tests', () => {
    initializeUnitTest((testEnv) => {

        test('createDOM()', () => {
            const {editor} = testEnv;
            let html!: string;

            editor.updateAndCommit(() => {
                const details = $createDetailsNode();
                html = details.createDOM(editorConfig, editor).outerHTML;
            });

            expect(html).toBe(`<details><summary contenteditable="false"></summary></details>`);
        });

        test('exportDOM()', () => {
            const {editor} = testEnv;
            let html!: string;

            editor.updateAndCommit(() => {
                const details = $createDetailsNode();
                html = (details.exportDOM(editor).element as HTMLElement).outerHTML;
            });

            expect(html).toBe(`<details><summary></summary></details>`);
        });


    });
})