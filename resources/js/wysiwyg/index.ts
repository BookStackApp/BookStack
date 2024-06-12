import {createEditor, CreateEditorArgs} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {registerRichText} from '@lexical/rich-text';
import {mergeRegister} from '@lexical/utils';
import {getNodesForPageEditor} from './nodes';
import {buildEditorUI} from "./ui";
import {setEditorContentFromHtml} from "./actions";

export function createPageEditorInstance(editArea: HTMLElement) {
    const config: CreateEditorArgs = {
        namespace: 'BookStackPageEditor',
        nodes: getNodesForPageEditor(),
        onError: console.error,
    };

    const startingHtml = editArea.innerHTML;

    const editor = createEditor(config);
    editor.setRootElement(editArea);

    mergeRegister(
        registerRichText(editor),
        registerHistory(editor, createEmptyHistoryState(), 300),
    );

    setEditorContentFromHtml(editor, startingHtml);

    const debugView = document.getElementById('lexical-debug');
    editor.registerUpdateListener(({editorState}) => {
        console.log('editorState', editorState.toJSON());
        if (debugView) {
            debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
        }
    });

    buildEditorUI(editArea, editor);

    // Example of creating, registering and using a custom command

    // const SET_BLOCK_CALLOUT_COMMAND = createCommand();
    // editor.registerCommand(SET_BLOCK_CALLOUT_COMMAND, (category: CalloutCategory = 'info') => {
    //     const selection = $getSelection();
    //     const blockElement = $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]);
    //     if ($isCalloutNode(blockElement)) {
    //         $setBlocksType(selection, $createParagraphNode);
    //     } else {
    //         $setBlocksType(selection, () => $createCalloutNode(category));
    //     }
    //     return true;
    // }, COMMAND_PRIORITY_LOW);
    //
    // const button = document.getElementById('lexical-button');
    // button.addEventListener('click', event => {
    //     editor.dispatchCommand(SET_BLOCK_CALLOUT_COMMAND, 'info');
    // });
}
