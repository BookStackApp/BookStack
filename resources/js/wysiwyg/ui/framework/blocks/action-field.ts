import {EditorContainerUiElement, EditorUiElement} from "../core";
import {el} from "../../../utils/dom";
import {EditorFormField} from "../forms";
import {EditorButton} from "../buttons";


export class EditorActionField extends EditorContainerUiElement {
    protected input: EditorFormField;
    protected action: EditorButton;

    constructor(input: EditorFormField, action: EditorButton) {
        super([input, action]);

        this.input = input;
        this.action = action;
    }

    buildDOM(): HTMLElement {
        return el('div', {
            class: 'editor-action-input-container',
        }, [
            this.input.getDOMElement(),
            this.action.getDOMElement(),
        ]);
    }
}
