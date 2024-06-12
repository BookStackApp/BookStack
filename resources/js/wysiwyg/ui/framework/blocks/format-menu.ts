import {el} from "../../../helpers";
import {EditorUiStateUpdate, EditorContainerUiElement} from "../core";
import {EditorButton} from "../buttons";
import {handleDropdown} from "../helpers/dropdowns";

export class EditorFormatMenu extends EditorContainerUiElement {
    buildDOM(): HTMLElement {
        const childElements: HTMLElement[] = this.getChildren().map(child => child.getDOMElement());
        const menu = el('div', {
            class: 'editor-format-menu-dropdown editor-dropdown-menu editor-menu-list',
            hidden: 'true',
        }, childElements);

        const toggle = el('button', {
            class: 'editor-format-menu-toggle editor-button',
            type: 'button',
        }, [this.trans('Formats')]);

        const wrapper = el('div', {
            class: 'editor-format-menu editor-dropdown-menu-container',
        }, [toggle, menu]);

        handleDropdown(toggle, menu);

        return wrapper;
    }

    updateState(state: EditorUiStateUpdate) {
        super.updateState(state);

        for (const child of this.children) {
            if (child instanceof EditorButton && child.isActive()) {
                this.updateToggleLabel(child.getLabel());
                return;
            }
        }

        this.updateToggleLabel(this.trans('Formats'));
    }

    protected updateToggleLabel(text: string): void {
        const button = this.getDOMElement().querySelector('button');
        if (button) {
            button.innerText = text;
        }
    }
}