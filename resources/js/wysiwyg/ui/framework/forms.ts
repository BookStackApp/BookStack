import {
    EditorUiContext,
    EditorUiElement,
    EditorContainerUiElement,
    EditorUiBuilderDefinition,
    isUiBuilderDefinition
} from "./core";
import {uniqueId} from "../../../services/util";
import {el} from "../../utils/dom";

export interface EditorFormFieldDefinition {
    label: string;
    name: string;
    type: 'text' | 'select' | 'textarea' | 'checkbox' | 'hidden';
}

export interface EditorSelectFormFieldDefinition extends EditorFormFieldDefinition {
    type: 'select',
    valuesByLabel: Record<string, string>
}

export type EditorFormFields = (EditorFormFieldDefinition|EditorUiBuilderDefinition)[];

interface EditorFormTabDefinition {
    label: string;
    contents: EditorFormFields;
}

export interface EditorFormDefinition {
    submitText: string;
    action: (formData: FormData, context: EditorUiContext) => Promise<boolean>;
    fields: EditorFormFields;
}

export class EditorFormField extends EditorUiElement {
    protected definition: EditorFormFieldDefinition;

    constructor(definition: EditorFormFieldDefinition) {
        super();
        this.definition = definition;
    }

    setValue(value: string) {
        const input = this.getDOMElement().querySelector('input,select,textarea') as HTMLInputElement;
        if (this.definition.type === 'checkbox') {
            input.checked = Boolean(value);
        } else {
            input.value = value;
        }
        input.dispatchEvent(new Event('change'));
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
            const optionElems = labels.map(label => el('option', {value: options[label]}, [this.trans(label)]));
            input = el('select', {id, name: this.definition.name, class: 'editor-form-field-input'}, optionElems);
        } else if (this.definition.type === 'textarea') {
            input = el('textarea', {id, name: this.definition.name, class: 'editor-form-field-input'});
        } else if (this.definition.type === 'checkbox') {
            input = el('input', {id, name: this.definition.name, type: 'checkbox', class: 'editor-form-field-input-checkbox', value: 'true'});
        } else if (this.definition.type === 'hidden') {
            input = el('input', {id, name: this.definition.name, type: 'hidden'});
            return el('div', {hidden: 'true'}, [input]);
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
    protected onSuccessfulSubmit: null|(() => void) = null;

    constructor(definition: EditorFormDefinition) {
        let children: (EditorFormField|EditorUiElement)[] = definition.fields.map(fieldDefinition => {
            if (isUiBuilderDefinition(fieldDefinition)) {
                return fieldDefinition.build();
            }
            return new EditorFormField(fieldDefinition)
        });

        super(children);
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

    setOnSuccessfulSubmit(callback: () => void) {
        this.onSuccessfulSubmit = callback;
    }

    protected getFieldByName(name: string): EditorFormField|null {

        const search = (children: EditorUiElement[]): EditorFormField|null => {
            for (const child of children) {
                if (child instanceof EditorFormField && child.getName() === name) {
                    return child;
                } else if (child instanceof EditorContainerUiElement) {
                    const matchingChild = search(child.getChildren());
                    if (matchingChild) {
                        return matchingChild;
                    }
                }
            }

            return null;
        };

        return search(this.getChildren());
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

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(form as HTMLFormElement);
            const result = await this.definition.action(formData, this.getContext());
            if (result && this.onSuccessfulSubmit) {
                this.onSuccessfulSubmit();
            }
        });

        cancelButton.addEventListener('click', (event) => {
            if (this.onCancel) {
                this.onCancel();
            }
        });

        return form;
    }
}

export class EditorFormTab extends EditorContainerUiElement {

    protected definition: EditorFormTabDefinition;
    protected fields: EditorUiElement[];
    protected id: string;

    constructor(definition: EditorFormTabDefinition) {
        const fields = definition.contents.map(fieldDef => {
            if (isUiBuilderDefinition(fieldDef)) {
                return fieldDef.build();
            }
            return new EditorFormField(fieldDef)
        });

        super(fields);

        this.definition = definition;
        this.fields = fields;
        this.id = uniqueId();
    }

    public getLabel(): string {
        return this.getContext().translate(this.definition.label);
    }

    public getId(): string {
        return this.id;
    }

    protected buildDOM(): HTMLElement {
        return el(
            'div',
            {
                class: 'editor-form-tab-content',
                role: 'tabpanel',
                id: `editor-tabpanel-${this.id}`,
                'aria-labelledby': `editor-tab-${this.id}`,
            },
            this.fields.map(f => f.getDOMElement())
        );
    }
}
export class EditorFormTabs extends EditorContainerUiElement {

    protected definitions: EditorFormTabDefinition[] = [];
    protected tabs: EditorFormTab[] = [];

    constructor(definitions: EditorFormTabDefinition[]) {
        const tabs: EditorFormTab[] = definitions.map(d => new EditorFormTab(d));
        super(tabs);

        this.definitions = definitions;
        this.tabs = tabs;
    }

    protected buildDOM(): HTMLElement {
        const controls: HTMLElement[] = [];
        const contents: HTMLElement[] = [];

        const selectTab = (tabIndex: number) => {
            for (let i = 0; i < controls.length; i++) {
                controls[i].setAttribute('aria-selected', (i === tabIndex) ? 'true' : 'false');
            }
            for (let i = 0; i < contents.length; i++) {
                contents[i].hidden = !(i === tabIndex);
            }
        };

        for (const tab of this.tabs) {
            const button = el('button', {
                class: 'editor-form-tab-control',
                type: 'button',
                role: 'tab',
                id: `editor-tab-${tab.getId()}`,
                'aria-controls': `editor-tabpanel-${tab.getId()}`
            }, [tab.getLabel()]);
            contents.push(tab.getDOMElement());
            controls.push(button);

            button.addEventListener('click', event => {
                selectTab(controls.indexOf(button));
            });
        }

        selectTab(0);

        return el('div', {class: 'editor-form-tab-container'}, [
            el('div', {class: 'editor-form-tab-controls'}, controls),
            el('div', {class: 'editor-form-tab-contents'}, contents),
        ]);
    }
}