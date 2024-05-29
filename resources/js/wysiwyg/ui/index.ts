import {
    $getSelection,
    COMMAND_PRIORITY_LOW,
    LexicalEditor,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {getMainEditorFullToolbar} from "./toolbars";

export function buildEditorUI(element: HTMLElement, editor: LexicalEditor) {
    const toolbar = getMainEditorFullToolbar();
    toolbar.setContext({editor});
    element.before(toolbar.getDOMElement());

    // Update button states on editor selection change
    editor.registerCommand(SELECTION_CHANGE_COMMAND, () => {
        const selection = $getSelection();
        toolbar.updateState({editor, selection});
        return false;
    }, COMMAND_PRIORITY_LOW);
}