import {
    $getSelection,
    COMMAND_PRIORITY_LOW,
    LexicalEditor,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {getMainEditorFullToolbar} from "./toolbars";
import {EditorUIManager} from "./framework/manager";
import {image as imageFormDefinition, link as linkFormDefinition, source as sourceFormDefinition} from "./defaults/form-definitions";
import {DecoratorListener} from "lexical/LexicalEditor";
import type {NodeKey} from "lexical/LexicalNode";
import {EditorDecoratorAdapter} from "./framework/decorator";
import {ImageDecorator} from "./decorators/image";
import {EditorUiContext} from "./framework/core";

export function buildEditorUI(element: HTMLElement, editor: LexicalEditor) {
    const manager = new EditorUIManager();
    const context: EditorUiContext = {
        editor,
        manager,
        translate: (text: string): string => text,
        lastSelection: null,
    };
    manager.setContext(context);

    // Create primary toolbar
    const toolbar = getMainEditorFullToolbar();
    toolbar.setContext(context);
    element.before(toolbar.getDOMElement());

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

    // Register decorator listener
    // Maybe move to manager?
    manager.registerDecoratorType('image', ImageDecorator);
    const domDecorateListener: DecoratorListener<EditorDecoratorAdapter> = (decorators: Record<NodeKey, EditorDecoratorAdapter>) => {
        const keys = Object.keys(decorators);
        for (const key of keys) {
            const decoratedEl = editor.getElementByKey(key);
            const adapter = decorators[key];
            const decorator = manager.getDecorator(adapter.type, key);
            decorator.setNode(adapter.getNode());
            const decoratorEl = decorator.render(context);
            if (decoratedEl) {
                decoratedEl.append(decoratorEl);
            }
        }
    }
    editor.registerDecoratorListener(domDecorateListener);

    // Update button states on editor selection change
    editor.registerCommand(SELECTION_CHANGE_COMMAND, () => {
        const selection = $getSelection();
        toolbar.updateState({editor, selection});
        context.lastSelection = selection;
        return false;
    }, COMMAND_PRIORITY_LOW);
}