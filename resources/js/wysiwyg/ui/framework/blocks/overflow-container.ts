import {EditorContainerUiElement, EditorUiElement} from "../core";
import {EditorDropdownButton} from "./dropdown-button";
import moreHorizontal from "@icons/editor/more-horizontal.svg"
import {el} from "../../../utils/dom";


export class EditorOverflowContainer extends EditorContainerUiElement {

    protected size: number;
    protected overflowButton: EditorDropdownButton;
    protected content: EditorUiElement[];

    constructor(size: number, children: EditorUiElement[]) {
        super(children);
        this.size = size;
        this.content = children;
        this.overflowButton = new EditorDropdownButton({
            button: {
                label: 'More',
                icon: moreHorizontal,
            },
            hideOnAction: false,
        }, []);
        this.addChildren(this.overflowButton);
    }

    protected buildDOM(): HTMLElement {
        const slicePosition = this.content.length > this.size ? this.size - 1 : this.size;
        const visibleChildren = this.content.slice(0, slicePosition);
        const invisibleChildren = this.content.slice(slicePosition);

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