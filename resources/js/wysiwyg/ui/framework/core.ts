import {BaseSelection, LexicalEditor} from "lexical";
import {EditorUIManager} from "./manager";
import {el} from "../../helpers";

export type EditorUiStateUpdate = {
    editor: LexicalEditor,
    selection: BaseSelection|null,
};

export type EditorUiContext = {
    editor: LexicalEditor,
    editorDOM: HTMLElement,
    containerDOM: HTMLElement,
    translate: (text: string) => string,
    manager: EditorUIManager,
    lastSelection: BaseSelection|null,
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

    trans(text: string) {
        return this.getContext().translate(text);
    }

    updateState(state: EditorUiStateUpdate): void {
        return;
    }
}

export class EditorContainerUiElement extends EditorUiElement {
    protected children : EditorUiElement[] = [];

    constructor(children: EditorUiElement[]) {
        super();
        this.children.push(...children);
    }

    protected buildDOM(): HTMLElement {
        return el('div', {}, this.getChildren().map(child => child.getDOMElement()));
    }

    getChildren(): EditorUiElement[] {
        return this.children;
    }

    protected addChildren(...children: EditorUiElement[]): void {
        this.children.push(...children);
    }

    protected removeChildren(...children: EditorUiElement[]): void {
        for (const child of children) {
            this.removeChild(child);
        }
    }

    protected removeChild(child: EditorUiElement) {
        const index = this.children.indexOf(child);
        if (index !== -1) {
            this.children.splice(index, 1);
        }
    }

    updateState(state: EditorUiStateUpdate): void {
        for (const child of this.children) {
            child.updateState(state);
        }
    }

    setContext(context: EditorUiContext) {
        super.setContext(context);
        for (const child of this.getChildren()) {
            child.setContext(context);
        }
    }
}

export class EditorSimpleClassContainer extends EditorContainerUiElement {
    protected className;

    constructor(className: string, children: EditorUiElement[]) {
        super(children);
        this.className = className;
    }

    protected buildDOM(): HTMLElement {
        return el('div', {
            class: this.className,
        }, this.getChildren().map(child => child.getDOMElement()));
    }
}

