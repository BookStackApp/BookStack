import {
    $createParagraphNode,
    $getRoot,
    $getSelection,
    COMMAND_PRIORITY_LOW,
    createCommand,
    createEditor, CreateEditorArgs,
} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {registerRichText} from '@lexical/rich-text';
import {$getNearestBlockElementAncestorOrThrow, mergeRegister} from '@lexical/utils';
import {$generateNodesFromDOM} from '@lexical/html';
import {$setBlocksType} from '@lexical/selection';
import {getNodesForPageEditor} from './nodes';
import {$createCalloutNode, $isCalloutNode, CalloutCategory} from './nodes/callout';

export function createPageEditorInstance(editArea: HTMLElement) {
    const config: CreateEditorArgs = {
        namespace: 'BookStackPageEditor',
        nodes: getNodesForPageEditor(),
        onError: console.error,
    };

    const startingHtml = editArea.innerHTML;
    const parser = new DOMParser();
    const dom = parser.parseFromString(startingHtml, 'text/html');

    const editor = createEditor(config);
    editor.setRootElement(editArea);

    mergeRegister(
        registerRichText(editor),
        registerHistory(editor, createEmptyHistoryState(), 300),
    );

    editor.update(() => {
        const startingNodes = $generateNodesFromDOM(editor, dom);
        const root = $getRoot();
        root.append(...startingNodes);
    });

    const debugView = document.getElementById('lexical-debug');
    editor.registerUpdateListener(({editorState}) => {
        console.log('editorState', editorState.toJSON());
        debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
    });

    // Example of creating, registering and using a custom command

    const SET_BLOCK_CALLOUT_COMMAND = createCommand();
    editor.registerCommand(SET_BLOCK_CALLOUT_COMMAND, (category: CalloutCategory = 'info') => {
        const selection = $getSelection();
        const blockElement = $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]);
        if ($isCalloutNode(blockElement)) {
            $setBlocksType(selection, $createParagraphNode);
        } else {
            $setBlocksType(selection, () => $createCalloutNode(category));
        }
        return true;
    }, COMMAND_PRIORITY_LOW);

    const button = document.getElementById('lexical-button');
    button.addEventListener('click', event => {
        editor.dispatchCommand(SET_BLOCK_CALLOUT_COMMAND, 'info');
    });
}
