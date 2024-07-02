import {EditorDecorator} from "../framework/decorator";
import {el} from "../../helpers";
import {EditorUiContext} from "../framework/core";
import {CodeBlockNode} from "../../nodes/code-block";


export class CodeBlockDecorator extends EditorDecorator {

     render(context: EditorUiContext, element: HTMLElement): void {
        const codeNode = this.getNode() as CodeBlockNode;
        const preEl = element.querySelector('pre');
        if (preEl) {
            preEl.hidden = true;
        }

        const code = codeNode.__code;
        const language = codeNode.__language;
        const lines = code.split('\n').length;
        const height = (lines * 19.2) + 18 + 24;
        element.style.height = `${height}px`;

        let editor = null;
        const startTime = Date.now();

        // Todo - Handling click/edit control
         // Todo - Add toolbar button for code

        // @ts-ignore
        const renderEditor = (Code) => {
            editor = Code.wysiwygView(element, document, code, language);
            setTimeout(() => {
                element.style.height = '';
            }, 12);
        };

        // @ts-ignore
        window.importVersioned('code').then((Code) => {
            const timeout = (Date.now() - startTime < 20) ? 20 : 0;
            setTimeout(() => renderEditor(Code), timeout);
        });
    }
}