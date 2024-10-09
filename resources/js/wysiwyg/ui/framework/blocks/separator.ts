import {EditorUiElement} from "../core";
import {el} from "../../../utils/dom";

export class EditorSeparator extends EditorUiElement {
    buildDOM(): HTMLElement {
        return el('div', {
            class: 'editor-separator',
        });
    }
}
