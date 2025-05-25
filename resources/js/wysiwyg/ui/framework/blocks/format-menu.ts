import {EditorUiStateUpdate, EditorContainerUiElement} from "../core";
import {EditorButton} from "../buttons";
import {el} from "../../../utils/dom";

export class EditorFormatMenu extends EditorContainerUiElement {
    buildDOM(): HTMLElement {
        const childElements: HTMLElement[] = this.getChildren().map(child => child.getDOMElement());
        const menu = el('div', {
            class: 'editor-format-menu-dropdown editor-dropdown-menu editor-dropdown-menu-vertical',
            hidden: 'true',
        }, childElements);

        const toggle = el('button', {
            class: 'editor-format-menu-toggle editor-button',
            type: 'button',
        }, [this.trans('Formats')]);

        const wrapper = el('div', {
            class: 'editor-format-menu editor-dropdown-menu-container',
        }, [toggle, menu]);

        this.getContext().manager.dropdowns.handle({toggle : toggle, menu : menu});

        this.onEvent('button-action', () => {
            this.getContext().manager.dropdowns.closeAll();
        }, wrapper);

        return wrapper;
    }

    updateState(state: EditorUiStateUpdate) {
        super.updateState(state);

        for (const child of this.children) {
            if (child instanceof EditorButton && child.isActive()) {
                this.updateToggleLabel(child.getLabel());
                return;
            }

            if (child instanceof EditorContainerUiElement) {
                for (const grandchild of child.getChildren()) {
                    if (grandchild instanceof EditorButton && grandchild.isActive()) {
                        this.updateToggleLabel(grandchild.getLabel());
                        return;
                    }
                }
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