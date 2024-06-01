import {
    $getSelection,
    COMMAND_PRIORITY_LOW,
    LexicalEditor,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {getMainEditorFullToolbar} from "./toolbars";
import {EditorUIManager} from "./framework/manager";
import {link as linkFormDefinition} from "./defaults/form-definitions";

export function buildEditorUI(element: HTMLElement, editor: LexicalEditor) {
    const manager = new EditorUIManager();
    const context = {
        editor,
        manager,
        translate: (text: string): string => text,
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

    // Update button states on editor selection change
    editor.registerCommand(SELECTION_CHANGE_COMMAND, () => {
        const selection = $getSelection();
        toolbar.updateState({editor, selection});
        return false;
    }, COMMAND_PRIORITY_LOW);
}