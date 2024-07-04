import {el} from "../../../helpers";
import {handleDropdown} from "../helpers/dropdowns";
import {EditorContainerUiElement, EditorUiElement} from "../core";
import {EditorBasicButtonDefinition, EditorButton} from "../buttons";

export class EditorDropdownButton extends EditorContainerUiElement {
    protected button: EditorButton;
    protected childItems: EditorUiElement[];
    protected open: boolean = false;
    protected showOnHover: boolean = false;

    constructor(button: EditorBasicButtonDefinition|EditorButton, showOnHover: boolean, children: EditorUiElement[]) {
        super(children);
        this.childItems = children;
        this.showOnHover = showOnHover;

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

        handleDropdown({toggle : button, menu : menu,
            showOnHover: this.showOnHover,
            onOpen : () => {
            this.open = true;
            this.getContext().manager.triggerStateUpdateForElement(this.button);
        }, onClose : () => {
            this.open = false;
            this.getContext().manager.triggerStateUpdateForElement(this.button);
        }});

        return wrapper;
    }
}