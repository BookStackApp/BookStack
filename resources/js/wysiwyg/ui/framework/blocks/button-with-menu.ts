import {EditorContainerUiElement, EditorUiElement} from "../core";
import {el} from "../../../utils/dom";
import {EditorButton} from "../buttons";
import {EditorDropdownButton} from "./dropdown-button";
import caretDownIcon from "@icons/caret-down-large.svg";

export class EditorButtonWithMenu extends EditorContainerUiElement {
    protected button: EditorButton;
    protected dropdownButton: EditorDropdownButton;

    constructor(button: EditorButton, menuItems: EditorUiElement[]) {
        super([button]);

        this.button = button;
        this.dropdownButton = new EditorDropdownButton({
            button: {label: 'Menu', icon: caretDownIcon},
            showOnHover: false,
            direction: 'vertical',
            showAside: false,
        }, menuItems);
        this.addChildren(this.dropdownButton);
    }

    buildDOM(): HTMLElement {
        return el('div', {
            class: 'editor-button-with-menu-container',
        }, [
            this.button.getDOMElement(),
            this.dropdownButton.getDOMElement()
        ]);
    }
}
