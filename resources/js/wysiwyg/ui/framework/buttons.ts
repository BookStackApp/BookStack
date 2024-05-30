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
            class: 'editor-button',
        }, [this.definition.label]) as HTMLButtonElement;

        button.addEventListener('click', this.onClick.bind(this));

        return button;
    }

    protected onClick() {
        this.definition.action(this.getContext().editor);
    }

    updateActiveState(selection: BaseSelection|null) {
        const isActive = this.definition.isActive(selection);
        this.dom?.classList.toggle('editor-button-active', isActive);
    }

    updateState(state: EditorUiStateUpdate): void {
        this.updateActiveState(state.selection);
    }
}

export class FormatPreviewButton extends EditorButton {
    protected previewSampleElement: HTMLElement;

    constructor(previewSampleElement: HTMLElement,definition: EditorButtonDefinition) {
        super(definition);
        this.previewSampleElement = previewSampleElement;
    }

    protected buildDOM(): HTMLButtonElement {
        const button = super.buildDOM();
        button.innerHTML = '';

        const preview = el('span', {
            class: 'editor-button-format-preview'
        }, [this.definition.label]);

        const stylesToApply = this.getStylesFromPreview();
        console.log(stylesToApply);
        for (const style of Object.keys(stylesToApply)) {
            preview.style.setProperty(style, stylesToApply[style]);
        }

        button.append(preview);
        return button;
    }

    protected getStylesFromPreview(): Record<string, string> {
        const wrap = el('div', {style: 'display: none', hidden: 'true', class: 'page-content'});
        const sampleClone = this.previewSampleElement.cloneNode() as HTMLElement;
        sampleClone.textContent = this.definition.label;
        wrap.append(sampleClone);
        document.body.append(wrap);

        const propertiesToFetch = ['color', 'font-size', 'background-color', 'border-inline-start'];
        const propertiesToReturn: Record<string, string> = {};

        const computed = window.getComputedStyle(sampleClone);
        for (const property of propertiesToFetch) {
            propertiesToReturn[property] = computed.getPropertyValue(property);
        }
        wrap.remove();

        return propertiesToReturn;
    }
}