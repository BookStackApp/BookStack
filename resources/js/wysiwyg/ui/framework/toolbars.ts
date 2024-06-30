import {EditorContainerUiElement, EditorUiElement} from "./core";
import {el} from "../../helpers";

export type EditorContextToolbarDefinition = {
    selector: string;
    content: EditorUiElement[],
    displayTargetLocator?: (originalTarget: HTMLElement) => HTMLElement;
};

export class EditorContextToolbar extends EditorContainerUiElement {

    protected buildDOM(): HTMLElement {
        return el('div', {
            class: 'editor-context-toolbar',
        }, this.getChildren().map(child => child.getDOMElement()));
    }

    attachTo(target: HTMLElement) {
        const targetBounds = target.getBoundingClientRect();
        const dom = this.getDOMElement();
        const domBounds = dom.getBoundingClientRect();

        const targetMid = targetBounds.left + (targetBounds.width / 2);
        const targetLeft = targetMid - (domBounds.width / 2);
        dom.style.top = (targetBounds.bottom + 6) + 'px';
        dom.style.left = targetLeft + 'px';
    }

    insert(children: EditorUiElement[]) {
        this.addChildren(...children);
        const dom = this.getDOMElement();
        dom.append(...children.map(child => child.getDOMElement()));
    }

    empty() {
        const children = this.getChildren();
        for (const child of children) {
            child.getDOMElement().remove();
        }
        this.removeChildren(...children);
    }
}