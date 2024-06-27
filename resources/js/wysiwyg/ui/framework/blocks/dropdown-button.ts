import {el} from "../../../helpers";
import {handleDropdown} from "../helpers/dropdowns";
import {EditorContainerUiElement, EditorUiElement} from "../core";
import {EditorBasicButtonDefinition, EditorButton} from "../buttons";

export class EditorDropdownButton extends EditorContainerUiElement {
    protected button: EditorButton;
    protected childItems: EditorUiElement[];
    protected open: boolean = false;

    constructor(button: EditorBasicButtonDefinition|EditorButton, children: EditorUiElement[]) {
        super(children);
        this.childItems = children

        if (button instanceof EditorButton) {
            this.button = button;
        } else {
            this.button = new EditorButton({
                ...button,
                action() {
                    return false;
                },
                isActive: () => {
                    return this.open;
                }
            });
        }

        this.addChildren(this.button);
    }

    insertItems(...items: EditorUiElement[]) {
        this.addChildren(...items);
        this.childItems.push(...items);
    }

    protected buildDOM(): HTMLElement {
        const button = this.button.getDOMElement();

        const childElements: HTMLElement[] = this.childItems.map(child => child.getDOMElement());
        const menu = el('div', {
            class: 'editor-dropdown-menu',
            hidden: 'true',
        }, childElements);

        const wrapper = el('div', {
            class: 'editor-dropdown-menu-container',
        }, [button, menu]);

        handleDropdown(button, menu, () => {
            this.open = true;
            this.getContext().manager.triggerStateUpdate(this.button);
        }, () => {
            this.open = false;
            this.getContext().manager.triggerStateUpdate(this.button);
        });

        return wrapper;
    }
}