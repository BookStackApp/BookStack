import {createTestContext} from "lexical/__tests__/utils";
import {$createDetailsNode} from "@lexical/rich-text/LexicalDetailsNode";

const editorConfig = Object.freeze({
    namespace: '',
    theme: {
    },
});

describe('LexicalDetailsNode tests', () => {
    test('createDOM()', () => {
        const {editor} = createTestContext();
        let html!: string;

        editor.updateAndCommit(() => {
            const details = $createDetailsNode();
            html = details.createDOM(editorConfig, editor).outerHTML;
        });

        expect(html).toBe(`<details contenteditable="false"><summary contenteditable="false"></summary></details>`);
    });

    test('exportDOM()', () => {
        const {editor} = createTestContext();
        let html!: string;

        editor.updateAndCommit(() => {
            const details = $createDetailsNode();
            details.setSummary('Hello there<>!')
            html = (details.exportDOM(editor).element as HTMLElement).outerHTML;
        });

        expect(html).toBe(`<details><summary>Hello there&lt;&gt;!</summary></details>`);
    });
})