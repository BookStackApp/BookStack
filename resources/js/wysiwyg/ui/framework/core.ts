import {BaseSelection, LexicalEditor} from "lexical";
import {EditorUIManager} from "./manager";

import {el} from "../../utils/dom";

export type EditorUiStateUpdate = {
    editor: LexicalEditor;
    selection: BaseSelection|null;
};

export type EditorUiContext = {
    editor: LexicalEditor; // Lexical editor instance
    editorDOM: HTMLElement; // DOM element the editor is bound to
    containerDOM: HTMLElement; // DOM element which contains all editor elements
    scrollDOM: HTMLElement; // DOM element which is the main content scroll container
    translate: (text: string) => string; // Translate function
    error: (text: string|Error) => void; // Error reporting function
    manager: EditorUIManager; // UI Manager instance for this editor
    options: Record<string, any>; // General user options which may be used by sub elements
};

export interface EditorUiBuilderDefinition {
    build: () => EditorUiElement;
}

export function isUiBuilderDefinition(object: any): object is EditorUiBuilderDefinition {
    return 'build' in object;
}

export abstract class EditorUiElement {
    protected dom: HTMLElement|null = null;
    private context: EditorUiContext|null = null;
    private abortController: AbortController = new AbortController();

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

    rebuildDOM(): HTMLElement {
        const newDOM = this.buildDOM();
        this.dom?.replaceWith(newDOM);
        this.dom = newDOM;
        return this.dom;
    }

    trans(text: string) {
        return this.getContext().translate(text);
    }

    updateState(state: EditorUiStateUpdate): void {
        return;
    }

    emitEvent(name: string, data: object = {}): void {
        if (this.dom) {
            this.dom.dispatchEvent(new CustomEvent('editor::' + name, {detail: data, bubbles: true}));
        }
    }

    onEvent(name: string, callback: (data: object) => any, listenTarget: HTMLElement|null = null): void {
        const target = listenTarget || this.dom;
        if (target) {
            target.addEventListener('editor::' + name, ((event: CustomEvent) => {
                callback(event.detail);
            }) as EventListener, { signal: this.abortController.signal });
        }
    }

    teardown(): void {
        if (this.dom && this.dom.isConnected) {
            this.dom.remove();
        }
        this.abortController.abort('teardown');
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

    teardown() {
        for (const child of this.children) {
            child.teardown();
        }
        super.teardown();
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

