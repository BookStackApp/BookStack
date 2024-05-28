import {
    $getSelection,
    BaseSelection,
    COMMAND_PRIORITY_LOW,
    LexicalEditor,
    SELECTION_CHANGE_COMMAND
} from "lexical";
import {$createCalloutNode, $isCalloutNodeOfCategory} from "../nodes/callout";
import {selectionContainsNodeType, toggleSelectionBlockNodeType} from "../helpers";
import {EditorButton, EditorButtonDefinition} from "./editor-button";

const calloutButton: EditorButtonDefinition = {
    label: 'Info Callout',
    action(editor: LexicalEditor) {
        toggleSelectionBlockNodeType(
            editor,
            (node) => $isCalloutNodeOfCategory(node, 'info'),
            () => $createCalloutNode('info'),
        )
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, (node) => $isCalloutNodeOfCategory(node, 'info'));
    }
}

const toolbarButtonDefinitions: EditorButtonDefinition[] = [
    calloutButton,
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