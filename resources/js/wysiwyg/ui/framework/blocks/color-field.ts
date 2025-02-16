import {EditorContainerUiElement, EditorUiBuilderDefinition, EditorUiContext} from "../core";
import {EditorFormField, EditorFormFieldDefinition} from "../forms";
import {EditorColorPicker} from "./color-picker";
import {EditorDropdownButton} from "./dropdown-button";

import colorDisplayIcon from "@icons/editor/color-display.svg"

export class EditorColorField extends EditorContainerUiElement {
    protected input: EditorFormField;
    protected pickerButton: EditorDropdownButton;

    constructor(input: EditorFormField) {
        super([]);

        this.input = input;

        this.pickerButton = new EditorDropdownButton({
            button: { icon: colorDisplayIcon, label: 'Select color'}
        }, [
            new EditorColorPicker(this.onColorSelect.bind(this))
        ]);
        this.addChildren(this.pickerButton, this.input);
    }

    protected buildDOM(): HTMLElement {
        const dom = this.input.getDOMElement();
        dom.append(this.pickerButton.getDOMElement());
        dom.classList.add('editor-color-field-container');

        const field = dom.querySelector('input') as HTMLInputElement;
        field.addEventListener('change', () => {
            this.setIconColor(field.value);
        });

        return dom;
    }

    onColorSelect(color: string, context: EditorUiContext): void {
        this.input.setValue(color);
    }

    setIconColor(color: string) {
        const icon = this.getDOMElement().querySelector('svg .editor-icon-color-display');
        if (icon) {
            icon.setAttribute('fill', color || 'url(#pattern2)');
        }
    }
}

export function colorFieldBuilder(field: EditorFormFieldDefinition): EditorUiBuilderDefinition {
    return {
        build() {
            return new EditorColorField(new EditorFormField(field));
        }
    }
}