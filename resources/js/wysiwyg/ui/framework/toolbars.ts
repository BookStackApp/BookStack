import {EditorContainerUiElement, EditorUiElement} from "./core";

import {el} from "../../utils/dom";

export type EditorContextToolbarDefinition = {
    selector: string;
    content: () => EditorUiElement[],
    displayTargetLocator?: (originalTarget: HTMLElement) => HTMLElement;
};

export class EditorContextToolbar extends EditorContainerUiElement {

    protected target: HTMLElement;

    constructor(target: HTMLElement, children: EditorUiElement[]) {
        super(children);
        this.target = target;
    }

    protected buildDOM(): HTMLElement {
        return el('div', {
            class: 'editor-context-toolbar',
        }, this.getChildren().map(child => child.getDOMElement()));
    }

    updatePosition() {
        const editorBounds = this.getContext().scrollDOM.getBoundingClientRect();
        const targetBounds = this.target.getBoundingClientRect();
        const dom = this.getDOMElement();
        const domBounds = dom.getBoundingClientRect();

        const showing = targetBounds.bottom > editorBounds.top
            && targetBounds.top < editorBounds.bottom;

        dom.hidden = !showing;

        if (!this.target.isConnected) {
            // If our target is no longer in the DOM, tell the manager an update is needed.
            this.getContext().manager.triggerFutureStateRefresh();
            return;
        } else if (!showing) {
            return;
        }

        const showAbove: boolean = targetBounds.bottom + 6 + domBounds.height > editorBounds.bottom;
        dom.classList.toggle('is-above', showAbove);

        const targetMid = targetBounds.left + (targetBounds.width / 2);
        const targetLeft = targetMid - (domBounds.width / 2);
        if (showAbove) {
            dom.style.top = (targetBounds.top - 6 - domBounds.height) + 'px';
        } else {
            dom.style.top = (targetBounds.bottom + 6) + 'px';
        }
        dom.style.left = targetLeft + 'px';
    }

    insert(children: EditorUiElement[]) {
        this.addChildren(...children);
        const dom = this.getDOMElement();
        dom.append(...children.map(child => child.getDOMElement()));
    }
}