import {EditorUiContext, EditorUiElement, EditorUiStateUpdate} from "./base-elements";
import {el} from "../../helpers";

export class EditorContainerUiElement extends EditorUiElement {
    protected children : EditorUiElement[];

    constructor(children: EditorUiElement[]) {
        super();
        this.children = children;
    }

    protected buildDOM(): HTMLElement {
        return el('div', {}, this.getChildren().map(child => child.getDOMElement()));
    }

    getChildren(): EditorUiElement[] {
        return this.children;
    }

    updateState(state: EditorUiStateUpdate): void {
        for (const child of this.children) {
            child.updateState(state);
        }
    }

    setContext(context: EditorUiContext) {
        for (const child of this.getChildren()) {
            child.setContext(context);
        }
    }
}

export class EditorFormatMenu extends EditorContainerUiElement {
    buildDOM(): HTMLElement {
        return el('div', {
            class: 'editor-format-menu'
        }, this.getChildren().map(child => child.getDOMElement()));
    }

}