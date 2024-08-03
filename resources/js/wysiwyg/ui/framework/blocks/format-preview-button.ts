import {EditorButton, EditorButtonDefinition} from "../buttons";
import {el} from "../../../utils/dom";

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
        }, [this.getLabel()]);

        const stylesToApply = this.getStylesFromPreview();
        for (const style of Object.keys(stylesToApply)) {
            preview.style.setProperty(style, stylesToApply[style]);
        }

        button.append(preview);
        return button;
    }

    protected getStylesFromPreview(): Record<string, string> {
        const wrap = el('div', {style: 'display: none', hidden: 'true', class: 'page-content'});
        const sampleClone = this.previewSampleElement.cloneNode() as HTMLElement;
        sampleClone.textContent = this.getLabel();
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