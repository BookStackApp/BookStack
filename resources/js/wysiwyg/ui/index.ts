import {LexicalEditor} from "lexical";
import {
    getCodeToolbarContent,
    getImageToolbarContent,
    getLinkToolbarContent,
    getMainEditorFullToolbar, getTableToolbarContent
} from "./toolbars";
import {EditorUIManager} from "./framework/manager";
import {image as imageFormDefinition, link as linkFormDefinition, source as sourceFormDefinition} from "./defaults/form-definitions";
import {ImageDecorator} from "./decorators/image";
import {EditorUiContext} from "./framework/core";
import {CodeBlockDecorator} from "./decorators/code-block";
import {DiagramDecorator} from "./decorators/diagram";

export function buildEditorUI(container: HTMLElement, element: HTMLElement, scrollContainer: HTMLElement, editor: LexicalEditor, options: Record<string, any>): EditorUiContext {
    const manager = new EditorUIManager();
    const context: EditorUiContext = {
        editor,
        containerDOM: container,
        editorDOM: element,
        scrollDOM: scrollContainer,
        manager,
        translate: (text: string): string => text,
        lastSelection: null,
        options,
    };
    manager.setContext(context);

    // Create primary toolbar
    manager.setToolbar(getMainEditorFullToolbar());

    // Register modals
    manager.registerModal('link', {
        title: 'Insert/Edit link',
        form: linkFormDefinition,
    });
    manager.registerModal('image', {
        title: 'Insert/Edit Image',
        form: imageFormDefinition
    });
    manager.registerModal('source', {
        title: 'Source code',
        form: sourceFormDefinition,
    });

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

    // Register image decorator listener
    manager.registerDecoratorType('image', ImageDecorator);
    manager.registerDecoratorType('code', CodeBlockDecorator);
    manager.registerDecoratorType('diagram', DiagramDecorator);

    return context;
}