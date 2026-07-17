import {
    DecoratorNode,
    DOMConversion,
    DOMConversionMap,
    DOMConversionOutput, DOMExportOutput,
    LexicalEditor, LexicalNode,
    SerializedLexicalNode,
    Spread
} from "lexical";
import type {EditorConfig} from "lexical/LexicalEditor";
import {EditorDecoratorAdapter} from "../../ui/framework/decorator";
import {CodeEditor} from "../../../components";
import {el} from "../../utils/dom";

export type SerializedCodeBlockNode = Spread<{
    language: string;
    id: string;
    code: string;
}, SerializedLexicalNode>

const getLanguageFromClassList = (classes: string) => {
    const langClasses = classes.split(' ').filter(cssClass => cssClass.startsWith('language-'));
    return (langClasses[0] || '').replace('language-', '');
};

export class CodeBlockNode extends DecoratorNode<EditorDecoratorAdapter> {
    __id: string = '';
    __language: string = '';
    __code: string = '';

    static getType(): string {
        return 'code-block';
    }

    static clone(node: CodeBlockNode): CodeBlockNode {
        const newNode = new CodeBlockNode(node.__language, node.__code, node.__key);
        newNode.__id = node.__id;
        return newNode;
    }

    constructor(language: string = '', code: string = '', key?: string) {
        super(key);
        this.__language = language;
        this.__code = code;
    }

    setLanguage(language: string): void {
        const self = this.getWritable();
        self.__language = language;
    }

    getLanguage(): string {
        const self = this.getLatest();
        return self.__language;
    }

    setCode(code: string): void {
        const self = this.getWritable();
        self.__code = code;
    }

    getCode(): string {
        const self = this.getLatest();
        return self.__code;
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    decorate(editor: LexicalEditor, config: EditorConfig): EditorDecoratorAdapter {
        return {
            type: 'code',
            getNode: () => this,
        };
    }

    isInline(): boolean {
        return false;
    }

    isIsolated() {
        return true;
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const codeBlock = el('pre', {
            id: this.__id || null,
        }, [
            el('code', {
                class: this.__language ? `language-${this.__language}` : null,
            }, [this.__code]),
        ]);

        return el('div', {class: 'editor-code-block-wrap'}, [codeBlock]);
    }

    updateDOM(prevNode: CodeBlockNode, dom: HTMLElement) {
        const code = dom.querySelector('code');
        if (!code) return false;

        if (prevNode.__language !== this.__language) {
            code.className = this.__language ? `language-${this.__language}` : '';
        }

        if (prevNode.__id !== this.__id) {
            dom.setAttribute('id', this.__id);
        }

        if (prevNode.__code !== this.__code) {
            code.textContent = this.__code;
        }

        return false;
    }

    exportDOM(editor: LexicalEditor): DOMExportOutput {
        const dom = this.createDOM(editor._config, editor);
        return {
            element: dom.querySelector('pre') as HTMLElement,
        };
    }

    static importDOM(): DOMConversionMap|null {
        return {
            pre(node: HTMLElement): DOMConversion|null {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {

                        const codeEl = element.querySelector('code');
                        const language = getLanguageFromClassList(element.className)
                                        || (codeEl && getLanguageFromClassList(codeEl.className))
                                        || '';

                        const code = codeEl ? (codeEl.textContent || '').trim() : (element.textContent || '').trim();
                        const node = $createCodeBlockNode(language, code);

                        if (element.id) {
                            node.setId(element.id);
                        }

                        return {
                            node,
                            after(childNodes): LexicalNode[] {
                                // Remove any child nodes that may get parsed since we're manually
                                // controlling the code contents.
                                return [];
                            },
                        };
                    },
                    priority: 3,
                };
            },
        };
    }

    exportJSON(): SerializedCodeBlockNode {
        return {
            type: 'code-block',
            version: 1,
            id: this.__id,
            language: this.__language,
            code: this.__code,
        };
    }

    static importJSON(serializedNode: SerializedCodeBlockNode): CodeBlockNode {
        const node = $createCodeBlockNode(serializedNode.language, serializedNode.code);
        node.setId(serializedNode.id || '');
        return node;
    }
}

export function $createCodeBlockNode(language: string = '', code: string = ''): CodeBlockNode {
    return new CodeBlockNode(language, code);
}

export function $isCodeBlockNode(node: LexicalNode | null | undefined) {
    return node instanceof CodeBlockNode;
}

export function $openCodeEditorForNode(editor: LexicalEditor, node: CodeBlockNode): void {
    const code = node.getCode();
    const language = node.getLanguage();

    // @ts-ignore
    const codeEditor = window.$components.first('code-editor') as CodeEditor;
    // TODO - Handle direction
    codeEditor.open(code, language, 'ltr', (newCode: string, newLang: string) => {
        editor.update(() => {
            node.setCode(newCode);
            node.setLanguage(newLang);
        });
        // TODO - Re-focus
    }, () => {
        // TODO - Re-focus
    });
}