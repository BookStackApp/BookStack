import {EditorContainerUiElement, EditorUiElement} from "../core";
import {el} from "../../../utils/dom";
import {EditorButton} from "../buttons";


export class EditorActionField extends EditorContainerUiElement {
    protected input: EditorUiElement;
    protected action: EditorButton;

    constructor(input: EditorUiElement, action: EditorButton) {
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
