import {
    $getSelection,
    COMMAND_PRIORITY_LOW,
    LexicalEditor,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {EditorButton, EditorButtonDefinition} from "./editor-button";
import {
    blockquoteButton, boldButton, codeButton,
    dangerCalloutButton,
    h2Button,
    h3Button, h4Button, h5Button,
    infoCalloutButton, italicButton, paragraphButton, redoButton, strikethroughButton, subscriptButton,
    successCalloutButton, superscriptButton, underlineButton, undoButton,
    warningCalloutButton
} from "./buttons";



const toolbarButtonDefinitions: EditorButtonDefinition[] = [
    undoButton, redoButton,

    infoCalloutButton, warningCalloutButton, dangerCalloutButton, successCalloutButton,
    h2Button, h3Button, h4Button, h5Button,
    blockquoteButton, paragraphButton,

    boldButton, italicButton, underlineButton, strikethroughButton,
    superscriptButton, subscriptButton, codeButton,
];

export function buildEditorUI(element: HTMLElement, editor: LexicalEditor) {
    const toolbarContainer = document.createElement('div');
    toolbarContainer.classList.add('editor-toolbar-container');

    const buttons = toolbarButtonDefinitions.map(definition => {
        return new EditorButton(definition, editor);
    });

    const buttonElements = buttons.map(button => button.getDOMElement());

    toolbarContainer.append(...buttonElements);
    element.before(toolbarContainer);

    // Update button states on editor selection change
    editor.registerCommand(SELECTION_CHANGE_COMMAND, () => {
        const selection = $getSelection();
        for (const button of buttons) {
            button.updateActiveState(selection);
        }
        return false;
    }, COMMAND_PRIORITY_LOW);
}