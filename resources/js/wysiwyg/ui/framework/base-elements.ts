import {BaseSelection, LexicalEditor} from "lexical";

export type EditorUiStateUpdate = {
    editor: LexicalEditor,
    selection: BaseSelection|null,
};

export type EditorUiContext = {
    editor: LexicalEditor,
};

export abstract class EditorUiElement {
    protected dom: HTMLElement|null = null;
    private context: EditorUiContext|null = null;

    protected abstract buildDOM(): HTMLElement;

    setContext(context: EditorUiContext): void {
        this.context = context;
    }

    getContext(): EditorUiContext {
        if (this.context === null) {
            throw new Error('Attempted to use EditorUIContext before it has been set');
        }

        return this.context;
    }

    getDOMElement(): HTMLElement {
        if (!this.dom) {
            this.dom = this.buildDOM();
        }

        return this.dom;
    }

    abstract updateState(state: EditorUiStateUpdate): void;
}