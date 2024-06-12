import {BaseSelection} from "lexical";
import {EditorUiContext, EditorUiElement, EditorUiStateUpdate} from "./core";
import {el} from "../../helpers";

export interface EditorBasicButtonDefinition {
    label: string;
}

export interface EditorButtonDefinition extends EditorBasicButtonDefinition {
    action: (context: EditorUiContext) => void;
    isActive: (selection: BaseSelection|null) => boolean;
}

export class EditorButton extends EditorUiElement {
    protected definition: EditorButtonDefinition;
    protected active: boolean = false;

    constructor(definition: EditorButtonDefinition) {
        super();
        this.definition = definition;
    }

    protected buildDOM(): HTMLButtonElement {
        const button = el('button', {
            type: 'button',
            class: 'editor-button',
        }, [this.getLabel()]) as HTMLButtonElement;

        button.addEventListener('click', this.onClick.bind(this));

        return button;
    }

    protected onClick() {
        this.definition.action(this.getContext());
    }

    updateActiveState(selection: BaseSelection|null) {
        this.active = this.definition.isActive(selection);
        this.dom?.classList.toggle('editor-button-active', this.active);
    }

    updateState(state: EditorUiStateUpdate): void {
        this.updateActiveState(state.selection);
    }

    isActive(): boolean {
        return this.active;
    }

    getLabel(): string {
        return this.trans(this.definition.label);
    }
}
