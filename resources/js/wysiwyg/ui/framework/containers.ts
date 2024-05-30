import {EditorUiContext, EditorUiElement, EditorUiStateUpdate} from "./core";
import {el} from "../../helpers";
import {EditorButton} from "./buttons";

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
        super.setContext(context);
        for (const child of this.getChildren()) {
            child.setContext(context);
        }
    }
}

export class EditorSimpleClassContainer extends EditorContainerUiElement {
    protected className;

    constructor(className: string, children: EditorUiElement[]) {
        super(children);
        this.className = className;
    }

    protected buildDOM(): HTMLElement {
        return el('div', {
            class: this.className,
        }, this.getChildren().map(child => child.getDOMElement()));
    }
}

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

        let clickListener: Function|null = null;

        const hide = () => {
            menu.hidden = true;
            if (clickListener) {
                window.removeEventListener('click', clickListener as EventListener);
            }
        };

        const show = () => {
            menu.hidden = false
            clickListener = (event: MouseEvent) => {
                if (!wrapper.contains(event.target as HTMLElement)) {
                    hide();
                }
            }
            window.addEventListener('click', clickListener as EventListener);
        };

        toggle.addEventListener('click', event => {
            menu.hasAttribute('hidden') ? show() : hide();
        });
        menu.addEventListener('mouseleave', hide);

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