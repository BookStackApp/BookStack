import {EditorUiContext, EditorUiElement} from "./core";
import {EditorContainerUiElement} from "./containers";
import {el} from "../../helpers";

export interface EditorFormFieldDefinition {
    label: string;
    name: string;
    type: 'text' | 'select' | 'textarea';
}

export interface EditorSelectFormFieldDefinition extends EditorFormFieldDefinition {
    type: 'select',
    valuesByLabel: Record<string, string>
}

export interface EditorFormDefinition {
    submitText: string;
    action: (formData: FormData, context: EditorUiContext) => boolean;
    fields: EditorFormFieldDefinition[];
}

export class EditorFormField extends EditorUiElement {
    protected definition: EditorFormFieldDefinition;

    constructor(definition: EditorFormFieldDefinition) {
        super();
        this.definition = definition;
    }

    setValue(value: string) {
        const input = this.getDOMElement().querySelector('input,select,textarea') as HTMLInputElement;
        input.value = value;
    }

    getName(): string {
        return this.definition.name;
    }

    protected buildDOM(): HTMLElement {
        const id = `editor-form-field-${this.definition.name}-${Date.now()}`;
        let input: HTMLElement;

        if (this.definition.type === 'select') {
            const options = (this.definition as EditorSelectFormFieldDefinition).valuesByLabel
            const labels = Object.keys(options);
            const optionElems = labels.map(label => el('option', {value: options[label]}, [label]));
            input = el('select', {id, name: this.definition.name, class: 'editor-form-field-input'}, optionElems);
        } else if (this.definition.type === 'textarea') {
            input = el('textarea', {id, name: this.definition.name, class: 'editor-form-field-input'});
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
    protected onCancel: null|(() => void) = null;

    constructor(definition: EditorFormDefinition) {
        super(definition.fields.map(fieldDefinition => new EditorFormField(fieldDefinition)));
        this.definition = definition;
    }

    setValues(values: Record<string, string>) {
        for (const name of Object.keys(values)) {
            const field = this.getFieldByName(name);
            if (field) {
                field.setValue(values[name]);
            }
        }
    }

    setOnCancel(callback: () => void) {
        this.onCancel = callback;
    }

    protected getFieldByName(name: string): EditorFormField|null {
        for (const child of this.children as EditorFormField[]) {
            if (child.getName() === name) {
                return child;
            }
        }

        return null;
    }

    protected buildDOM(): HTMLElement {
        const cancelButton = el('button', {type: 'button', class: 'editor-form-action-secondary'}, [this.trans('Cancel')]);
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
            if (this.onCancel) {
                this.onCancel();
            }
        });

        return form;
    }
}