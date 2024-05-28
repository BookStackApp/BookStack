import {BaseSelection, LexicalEditor} from "lexical";

export interface EditorButtonDefinition {
    label: string;
    action: (editor: LexicalEditor) => void;
    isActive: (selection: BaseSelection|null) => boolean;
}

export class EditorButton {
    #definition: EditorButtonDefinition;
    #editor: LexicalEditor;
    #dom: HTMLButtonElement;

    constructor(definition: EditorButtonDefinition, editor: LexicalEditor) {
        this.#definition = definition;
        this.#editor = editor;
        this.#dom = this.buildDOM();
    }

    private buildDOM(): HTMLButtonElement {
        const button = document.createElement("button");
        button.setAttribute('type', 'button');
        button.textContent = this.#definition.label;
        button.classList.add('editor-toolbar-button');

        button.addEventListener('click', event => {
            this.runAction();
        });

        return button;
    }

    getDOMElement(): HTMLButtonElement {
        return this.#dom;
    }

    runAction() {
        this.#definition.action(this.#editor);
    }

    updateActiveState(selection: BaseSelection|null) {
        const isActive = this.#definition.isActive(selection);
        this.#dom.classList.toggle('editor-toolbar-button-active', isActive);
    }
}