import {BaseSelection, LexicalEditor} from "lexical";
import {EditorUiElement, EditorUiStateUpdate} from "./base-elements";
import {el} from "../../helpers";

export interface EditorButtonDefinition {
    label: string;
    action: (editor: LexicalEditor) => void;
    isActive: (selection: BaseSelection|null) => boolean;
}

export class EditorButton extends EditorUiElement {
    protected definition: EditorButtonDefinition;

    constructor(definition: EditorButtonDefinition) {
        super();
        this.definition = definition;
    }

    protected buildDOM(): HTMLButtonElement {
        const button = el('button', {
            type: 'button',
            class: 'editor-toolbar-button',
        }, [this.definition.label]) as HTMLButtonElement;

        button.addEventListener('click', event => {
            this.definition.action(this.getContext().editor);
        });

        return button;
    }

    updateActiveState(selection: BaseSelection|null) {
        const isActive = this.definition.isActive(selection);
        this.dom?.classList.toggle('editor-toolbar-button-active', isActive);
    }

    updateState(state: EditorUiStateUpdate): void {
        this.updateActiveState(state.selection);
    }
}