import {EditorForm, EditorFormDefinition} from "./forms";
import {el} from "../../helpers";
import {EditorContainerUiElement} from "./core";
import closeIcon from "@icons/close.svg";

export interface EditorModalDefinition {
    title: string;
}

export interface EditorFormModalDefinition extends EditorModalDefinition {
    form: EditorFormDefinition;
}

export class EditorFormModal extends EditorContainerUiElement {
    protected definition: EditorFormModalDefinition;

    constructor(definition: EditorFormModalDefinition) {
        super([new EditorForm(definition.form)]);
        this.definition = definition;
    }

    show(defaultValues: Record<string, string>) {
        const dom = this.getDOMElement();
        document.body.append(dom);

        const form = this.getForm();
        form.setValues(defaultValues);
        form.setOnCancel(this.hide.bind(this));
    }

    hide() {
        this.getDOMElement().remove();
    }

    protected getForm(): EditorForm {
        return this.children[0] as EditorForm;
    }

    protected buildDOM(): HTMLElement {
        const closeButton = el('button', {
            class: 'editor-modal-close',
            type: 'button',
            title: this.trans('Close'),
        });
        closeButton.innerHTML = closeIcon;
        closeButton.addEventListener('click', this.hide.bind(this));

        const modal = el('div', {class: 'editor-modal editor-form-modal'}, [
            el('div', {class: 'editor-modal-header'}, [
                el('div', {class: 'editor-modal-title'}, [this.trans(this.definition.title)]),
                closeButton,
            ]),
            el('div', {class: 'editor-modal-body'}, [
                this.getForm().getDOMElement(),
            ]),
        ]);

        const wrapper = el('div', {class: 'editor-modal-wrapper'}, [modal]);

        wrapper.addEventListener('click', event => {
            if (event.target && !modal.contains(event.target as HTMLElement)) {
                this.hide();
            }
        });

        return wrapper;
    }
}