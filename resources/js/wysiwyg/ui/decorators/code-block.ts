import {EditorDecorator} from "../framework/decorator";
import {EditorUiContext} from "../framework/core";
import {$openCodeEditorForNode, CodeBlockNode} from "@lexical/rich-text/LexicalCodeBlockNode";
import {BaseSelection} from "lexical";
import {$selectionContainsNode, $selectSingleNode} from "../../utils/selection";


export class CodeBlockDecorator extends EditorDecorator {

    protected completedSetup: boolean = false;
    protected latestCode: string = '';
    protected latestLanguage: string = '';

    // @ts-ignore
    protected editor: any = null;

    setup(context: EditorUiContext, element: HTMLElement) {
        const codeNode = this.getNode() as CodeBlockNode;
        const preEl = element.querySelector('pre');
        if (!preEl) {
            return;
        }

        if (preEl) {
            preEl.hidden = true;
        }

        this.latestCode = codeNode.__code;
        this.latestLanguage = codeNode.__language;
        const lines = this.latestCode.split('\n').length;
        const height = (lines * 19.2) + 18 + 24;
        element.style.height = `${height}px`;

        const startTime = Date.now();

        element.addEventListener('click', event => {
            requestAnimationFrame(() => {
                context.editor.update(() => {
                    $selectSingleNode(this.getNode());
                });
            });
        });

        element.addEventListener('dblclick', event => {
            context.editor.getEditorState().read(() => {
                $openCodeEditorForNode(context.editor, (this.getNode() as CodeBlockNode));
            });
        });

        const selectionChange = (selection: BaseSelection|null): void => {
            element.classList.toggle('selected', $selectionContainsNode(selection, codeNode));
        };
        context.manager.onSelectionChange(selectionChange);
        this.onDestroy(() => {
            context.manager.offSelectionChange(selectionChange);
        });

        // @ts-ignore
        const renderEditor = (Code) => {
            this.editor = Code.wysiwygView(element, document, this.latestCode, this.latestLanguage);
            setTimeout(() => {
                element.style.height = '';
            }, 12);
        };

        // @ts-ignore
        window.importVersioned('code').then((Code) => {
            const timeout = (Date.now() - startTime < 20) ? 20 : 0;
            setTimeout(() => renderEditor(Code), timeout);
        });

        this.completedSetup = true;
    }

    update() {
        const codeNode = this.getNode() as CodeBlockNode;
        const code = codeNode.getCode();
        const language = codeNode.getLanguage();

        if (this.latestCode === code && this.latestLanguage === language) {
            return;
        }
        this.latestLanguage = language;
        this.latestCode = code;

        if (this.editor) {
            this.editor.setContent(code);
            this.editor.setMode(language, code);
        }
    }

    render(context: EditorUiContext, element: HTMLElement): void {
        if (this.completedSetup) {
            this.update();
        } else {
            this.setup(context, element);
        }
    }
}