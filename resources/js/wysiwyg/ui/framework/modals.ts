import {EditorForm, EditorFormDefinition} from "./forms";
import {EditorContainerUiElement} from "./core";
import closeIcon from "@icons/close.svg";
import {el} from "../../utils/dom";

export interface EditorModalDefinition {
    title: string;
}

export interface EditorFormModalDefinition extends EditorModalDefinition {
    form: EditorFormDefinition;
}

export class EditorFormModal extends EditorContainerUiElement {
    protected definition: EditorFormModalDefinition;
    protected key: string;

    constructor(definition: EditorFormModalDefinition, key: string) {
        super([new EditorForm(definition.form)]);
        this.definition = definition;
        this.key = key;
    }

    show(defaultValues: Record<string, string>) {
        const dom = this.getDOMElement();
        document.body.append(dom);

        const form = this.getForm();
        form.setValues(defaultValues);
        form.setOnCancel(this.hide.bind(this));
        form.setOnSuccessfulSubmit(this.hide.bind(this));

        this.getContext().manager.setModalActive(this.key, this);
    }

    hide() {
        this.getContext().manager.setModalInactive(this.key);
        this.teardown();
    }

    getForm(): EditorForm {
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