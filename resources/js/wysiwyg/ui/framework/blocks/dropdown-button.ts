import {EditorContainerUiElement, EditorUiElement} from "../core";
import {EditorBasicButtonDefinition, EditorButton} from "../buttons";
import {el} from "../../../utils/dom";
import {EditorMenuButton} from "./menu-button";

export type EditorDropdownButtonOptions = {
    showOnHover?: boolean;
    direction?: 'vertical'|'horizontal';
    showAside?: boolean;
    hideOnAction?: boolean;
    button: EditorBasicButtonDefinition|EditorButton;
};

const defaultOptions: EditorDropdownButtonOptions = {
    showOnHover: false,
    direction: 'horizontal',
    showAside: undefined,
    hideOnAction: true,
    button: {label: 'Menu'},
}

export class EditorDropdownButton extends EditorContainerUiElement {
    protected button: EditorButton;
    protected childItems: EditorUiElement[];
    protected open: boolean = false;
    protected options: EditorDropdownButtonOptions;

    constructor(options: EditorDropdownButtonOptions, children: EditorUiElement[]) {
        super(children);
        this.childItems = children;
        this.options = Object.assign({}, defaultOptions, options);

        if (options.button instanceof EditorButton) {
            this.button = options.button;
        } else {
            const type = options.button.format === 'long' ? EditorMenuButton : EditorButton;
            this.button = new type({
                ...options.button,
                action() {
                    return false;
                },
                isActive: () => {
                    return this.open;
                },
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
            class: `editor-dropdown-menu editor-dropdown-menu-${this.options.direction}`,
            hidden: 'true',
        }, childElements);

        const wrapper = el('div', {
            class: 'editor-dropdown-menu-container',
        }, [button, menu]);

        this.getContext().manager.dropdowns.handle({toggle: button, menu : menu,
            showOnHover: this.options.showOnHover,
            showAside: typeof this.options.showAside === 'boolean' ? this.options.showAside : (this.options.direction === 'vertical'),
            onOpen : () => {
            this.open = true;
            this.getContext().manager.triggerStateUpdateForElement(this.button);
        }, onClose : () => {
            this.open = false;
            this.getContext().manager.triggerStateUpdateForElement(this.button);
        }});

        if (this.options.hideOnAction) {
            this.onEvent('button-action', () => {
                this.getContext().manager.dropdowns.closeAll();
            }, wrapper);
        }

        return wrapper;
    }
}