import {LexicalEditor} from "lexical";
import {
    getCodeToolbarContent, getDetailsToolbarContent,
    getImageToolbarContent,
    getLinkToolbarContent,
    getMainEditorFullToolbar, getTableToolbarContent
} from "./defaults/toolbars";
import {EditorUIManager} from "./framework/manager";
import {EditorUiContext} from "./framework/core";
import {CodeBlockDecorator} from "./decorators/code-block";
import {DiagramDecorator} from "./decorators/diagram";
import {modals} from "./defaults/modals";

export function buildEditorUI(container: HTMLElement, element: HTMLElement, scrollContainer: HTMLElement, editor: LexicalEditor, options: Record<string, any>): EditorUiContext {
    const manager = new EditorUIManager();
    const context: EditorUiContext = {
        editor,
        containerDOM: container,
        editorDOM: element,
        scrollDOM: scrollContainer,
        manager,
        translate(text: string): string {
            const translations = options.translations;
            return translations[text] || text;
        },
        error(error: string|Error): void {
            const message = error instanceof Error ? error.message : error;
            window.$events.error(message); // TODO - Translate
        },
        options,
    };
    manager.setContext(context);

    // Create primary toolbar
    manager.setToolbar(getMainEditorFullToolbar(context));

    // Register modals
    for (const key of Object.keys(modals)) {
        manager.registerModal(key, modals[key]);
    }

    // Register context toolbars
    manager.registerContextToolbar('image', {
        selector: 'img:not([drawio-diagram] img)',
        content: getImageToolbarContent(),
    });
    manager.registerContextToolbar('link', {
        selector: 'a',
        content: getLinkToolbarContent(),
        displayTargetLocator(originalTarget: HTMLElement): HTMLElement {
            const image = originalTarget.querySelector('img');
            return image || originalTarget;
        }
    });
    manager.registerContextToolbar('code', {
        selector: '.editor-code-block-wrap',
        content: getCodeToolbarContent(),
    });
    manager.registerContextToolbar('table', {
        selector: 'td,th',
        content: getTableToolbarContent(),
        displayTargetLocator(originalTarget: HTMLElement): HTMLElement {
            return originalTarget.closest('table') as HTMLTableElement;
        }
    });
    manager.registerContextToolbar('details', {
        selector: 'details',
        content: getDetailsToolbarContent(),
    });

    // Register image decorator listener
    manager.registerDecoratorType('code', CodeBlockDecorator);
    manager.registerDecoratorType('diagram', DiagramDecorator);

    return context;
}