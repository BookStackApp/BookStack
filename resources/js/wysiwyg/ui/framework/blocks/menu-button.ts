import {EditorButton} from "../buttons";
import {el} from "../../../utils/dom";
import arrowIcon from "@icons/chevron-right.svg"

export class EditorMenuButton extends EditorButton {
    protected buildDOM(): HTMLButtonElement {
        const dom = super.buildDOM();

        const icon = el('div', {class: 'editor-menu-button-icon'});
        icon.innerHTML = arrowIcon;
        dom.append(icon);

        return dom;
    }
}