import {BaseSelection} from "lexical";
import {EditorUiContext, EditorUiElement, EditorUiStateUpdate} from "./core";

import {el} from "../../utils/dom";

export interface EditorBasicButtonDefinition {
    label: string;
    icon?: string|undefined;
    format?: 'small' | 'long';
}

export interface EditorButtonDefinition extends EditorBasicButtonDefinition {
    /**
     * The action to perform when the button is used.
     * This can return false to indicate that the completion of the action should
     * NOT be communicated to parent UI elements, which is what occurs by default.
     */
    action: (context: EditorUiContext, button: EditorButton) => void|false|Promise<void|boolean>;
    isActive: (selection: BaseSelection|null, context: EditorUiContext) => boolean;
    isDisabled?: (selection: BaseSelection|null, context: EditorUiContext) => boolean;
    setup?: (context: EditorUiContext, button: EditorButton) => void;
}

export class EditorButton extends EditorUiElement {
    protected definition: EditorButtonDefinition;
    protected active: boolean = false;
    protected completedSetup: boolean = false;
    protected disabled: boolean = false;

    constructor(definition: EditorButtonDefinition|EditorBasicButtonDefinition) {
        super();

        if ((definition as EditorButtonDefinition).action !== undefined) {
            this.definition = definition as EditorButtonDefinition;
        } else {
            this.definition = {
                ...definition,
                action() {
                    return false;
                },
                isActive: () => {
                    return false;
                }
            };
        }
    }

    setContext(context: EditorUiContext) {
        super.setContext(context);

        if (this.definition.setup && !this.completedSetup) {
            this.definition.setup(context, this);
            this.completedSetup = true;
        }
    }

    protected buildDOM(): HTMLButtonElement {
        const label = this.getLabel();
        const format = this.definition.format || 'small';
        const children: (string|HTMLElement)[] = [];

        if (this.definition.icon || format === 'long') {
            const icon = el('div', {class: 'editor-button-icon'});
            icon.innerHTML = this.definition.icon || '';
            children.push(icon);
        }

        if (!this.definition.icon ||format === 'long') {
            const text = el('div', {class: 'editor-button-text'}, [label]);
            children.push(text);
        }

        const button = el('button', {
            type: 'button',
            class: `editor-button editor-button-${format}`,
            title: this.definition.icon ? label : null,
            disabled: this.disabled ? 'true' : null,
        }, children) as HTMLButtonElement;

        button.addEventListener('click', this.onClick.bind(this));

        return button;
    }

    protected onClick() {
        const result = this.definition.action(this.getContext(), this);
        if (result instanceof Promise) {
            result.then(result => {
                if (result === false) {
                    this.emitEvent('button-action');
                }
            });
        } else if (result !== false) {
            this.emitEvent('button-action');
        }
    }

    protected updateActiveState(selection: BaseSelection|null) {
        const isActive = this.definition.isActive(selection, this.getContext());
        this.setActiveState(isActive);
    }

    protected updateDisabledState(selection: BaseSelection|null) {
        if (this.definition.isDisabled) {
            const isDisabled = this.definition.isDisabled(selection, this.getContext());
            this.toggleDisabled(isDisabled);
        }
    }

    setActiveState(active: boolean) {
        this.active = active;
        this.dom?.classList.toggle('editor-button-active', this.active);
    }

    updateState(state: EditorUiStateUpdate): void {
        this.updateActiveState(state.selection);
        this.updateDisabledState(state.selection);
    }

    isActive(): boolean {
        return this.active;
    }

    getLabel(): string {
        return this.trans(this.definition.label);
    }

    toggleDisabled(disabled: boolean) {
        this.disabled = disabled;
        if (disabled) {
            this.dom?.setAttribute('disabled', 'true');
        } else {
            this.dom?.removeAttribute('disabled');
        }
    }
}
