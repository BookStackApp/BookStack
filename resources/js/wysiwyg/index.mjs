import {$getRoot, createEditor, ElementNode} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {HeadingNode, QuoteNode, registerRichText} from '@lexical/rich-text';
import {mergeRegister} from '@lexical/utils';
import {$generateNodesFromDOM} from '@lexical/html';

class CalloutParagraph extends ElementNode {
    __category = 'info';

    static getType() {
        return 'callout';
    }

    static clone(node) {
        return new CalloutParagraph(node.__category, node.__key);
    }

    constructor(category, key) {
        super(key);
        this.__category = category;
    }

    createDOM(_config, _editor) {
        const dom = document.createElement('p');
        dom.classList.add('callout', this.__category || '');
        return dom;
    }

    updateDOM(prevNode, dom) {
        // Returning false tells Lexical that this node does not need its
        // DOM element replacing with a new copy from createDOM.
        return false;
    }

    static importDOM() {
        return {
            p: node => {
                if (node.classList.contains('callout')) {
                    return {
                        conversion: element => {
                            let category = 'info';
                            const categories = ['info', 'success', 'warning', 'danger'];

                            for (const c of categories) {
                                if (element.classList.contains(c)) {
                                    category = c;
                                    break;
                                }
                            }

                            return {
                                node: new CalloutParagraph(category),
                            };
                        },
                        priority: 3,
                    }
                }
                return null;
            }
        }
    }

    exportJSON() {
        return {
            ...super.exportJSON(),
            type: 'callout',
            version: 1,
            category: this.__category,
        };
    }
}

// TODO - Extract callout to own file
// TODO - Add helper functions
//   https://lexical.dev/docs/concepts/nodes#creating-custom-nodes

export function createPageEditorInstance(editArea) {
    console.log('creating editor', editArea);

    const config = {
        namespace: 'BookStackPageEditor',
        nodes: [HeadingNode, QuoteNode, CalloutParagraph],
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
}