import {EditorContainerUiElement, EditorUiElement} from "../core";
import {el} from "../../../helpers";
import {EditorDropdownButton} from "./dropdown-button";
import moreHorizontal from "@icons/editor/more-horizontal.svg"


export class EditorOverflowContainer extends EditorContainerUiElement {

    protected size: number;
    protected overflowButton: EditorDropdownButton;
    protected content: EditorUiElement[];

    constructor(size: number, children: EditorUiElement[]) {
        super(children);
        this.size = size;
        this.content = children;
        this.overflowButton = new EditorDropdownButton({
            label: 'More',
            icon: moreHorizontal,
        }, []);
        this.addChildren(this.overflowButton);
    }

    protected buildDOM(): HTMLElement {
        const visibleChildren = this.content.slice(0, this.size);
        const invisibleChildren = this.content.slice(this.size);

        const visibleElements = visibleChildren.map(child => child.getDOMElement());
        if (invisibleChildren.length > 0) {
            this.removeChildren(...invisibleChildren);
            this.overflowButton.insertItems(...invisibleChildren);
            visibleElements.push(this.overflowButton.getDOMElement());
        }

        return el('div', {
            class: 'editor-overflow-container',
        }, visibleElements);
    }


}