import {EditorButton} from "../ui/framework/buttons";
import {EditorUiContext} from "../ui/framework/core";
import {EditorOverflowContainer} from "../ui/framework/blocks/overflow-container";

type EditorApiButtonOptions = {
    label?: string;
    icon?: string;
    action: () => void;
};

export class EditorApiButton {
    readonly #button: EditorButton;
    #isActive: boolean = false;

    constructor(options: EditorApiButtonOptions, context: EditorUiContext) {
        this.#button = new EditorButton({
            label: options.label || '',
            icon: options.icon || '',
            action: () => {
                options.action();
            },
            isActive: () => this.#isActive,
        });
        this.#button.setContext(context);
    }

    setActive(active: boolean = true): void {
        this.#isActive = active;
        this.#button.setActiveState(active);
    }

    _getOriginalModel() {
        return this.#button;
    }
}

export class EditorApiToolbarSection {
    readonly #section: EditorOverflowContainer;
    label: string;

    constructor(section: EditorOverflowContainer) {
        this.#section = section;
        this.label = section.getLabel();
    }

    getLabel(): string {
        return this.#section.getLabel();
    }

    addButton(button: EditorApiButton, targetIndex: number = -1): void {
        this.#section.addChild(button._getOriginalModel(), targetIndex);
        this.#section.rebuildDOM();
    }
}


export class EditorApiUiModule {
    readonly #context: EditorUiContext;
    
    constructor(context: EditorUiContext) {
        this.#context = context;
    }
    
    createButton(options: EditorApiButtonOptions): EditorApiButton {
        return new EditorApiButton(options, this.#context);
    }

    getMainToolbarSections(): EditorApiToolbarSection[] {
        const toolbar = this.#context.manager.getToolbar();
        if (!toolbar) {
            return [];
        }

        const sections = toolbar.getChildren();
        return sections.filter(section => {
            return section instanceof EditorOverflowContainer;
        }).map(section => new EditorApiToolbarSection(section));
    }
}