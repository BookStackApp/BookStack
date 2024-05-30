import {EditorUiContext, EditorUiElement} from "./core";
import {EditorContainerUiElement} from "./containers";
import {el} from "../../helpers";

export interface EditorFormFieldDefinition {
    label: string;
    name: string;
    type: 'text' | 'select';
}

export interface EditorSelectFormFieldDefinition extends EditorFormFieldDefinition {
    type: 'select',
    valuesByLabel: Record<string, string>
}

export interface EditorFormDefinition {
    submitText: string;
    cancelText: string;
    action: (formData: FormData, context: EditorUiContext) => boolean;
    cancel: () => void;
    fields: EditorFormFieldDefinition[];
}

export class EditorFormField extends EditorUiElement {
    protected definition: EditorFormFieldDefinition;

    constructor(definition: EditorFormFieldDefinition) {
        super();
        this.definition = definition;
    }

    protected buildDOM(): HTMLElement {
        const id = `editor-form-field-${this.definition.name}-${Date.now()}`;
        let input: HTMLElement;

        if (this.definition.type === 'select') {
            const options = (this.definition as EditorSelectFormFieldDefinition).valuesByLabel
            const labels = Object.keys(options);
            const optionElems = labels.map(label => el('option', {value: options[label]}, [label]));
            input = el('select', {id, name: this.definition.name, class: 'editor-form-field-input'}, optionElems);
        } else {
            input = el('input', {id, name: this.definition.name, class: 'editor-form-field-input'});
        }

        return el('div', {class: 'editor-form-field-wrapper'}, [
            el('label', {class: 'editor-form-field-label', for: id}, [this.trans(this.definition.label)]),
            input,
        ]);
    }
}

export class EditorForm extends EditorContainerUiElement {
    protected definition: EditorFormDefinition;

    constructor(definition: EditorFormDefinition) {
        super(definition.fields.map(fieldDefinition => new EditorFormField(fieldDefinition)));
        this.definition = definition;
    }

    protected buildDOM(): HTMLElement {
        const cancelButton = el('button', {type: 'button', class: 'editor-form-action-secondary'}, [this.trans(this.definition.cancelText)]);
        const form = el('form', {}, [
            ...this.children.map(child => child.getDOMElement()),
            el('div', {class: 'editor-form-actions'}, [
                cancelButton,
                el('button', {type: 'submit', class: 'editor-form-action-primary'}, [this.trans(this.definition.submitText)]),
            ])
        ]);

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const formData = new FormData(form as HTMLFormElement);
            this.definition.action(formData, this.getContext());
        });

        cancelButton.addEventListener('click', (event) => {
            this.definition.cancel();
        });

        return form;
    }
}