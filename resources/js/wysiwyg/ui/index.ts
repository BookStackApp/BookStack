import {
    $getSelection,
    COMMAND_PRIORITY_LOW,
    LexicalEditor,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {getMainEditorFullToolbar} from "./toolbars";
import {EditorUIManager} from "./framework/manager";
import {EditorForm} from "./framework/forms";
import {link} from "./defaults/form-definitions";

export function buildEditorUI(element: HTMLElement, editor: LexicalEditor) {
    const manager = new EditorUIManager();
    const context = {
        editor,
        manager,
        translate: (text: string): string => text,
    };

    // Create primary toolbar
    const toolbar = getMainEditorFullToolbar();
    toolbar.setContext(context);
    element.before(toolbar.getDOMElement());

    // Form test
    const linkForm = new EditorForm(link);
    linkForm.setContext(context);
    element.before(linkForm.getDOMElement());

    // Update button states on editor selection change
    editor.registerCommand(SELECTION_CHANGE_COMMAND, () => {
        const selection = $getSelection();
        toolbar.updateState({editor, selection});
        return false;
    }, COMMAND_PRIORITY_LOW);
}