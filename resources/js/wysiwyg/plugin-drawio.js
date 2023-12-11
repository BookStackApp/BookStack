import * as DrawIO from '../services/drawio';
import {wait} from '../services/util';

let pageEditor = null;
let currentNode = null;

/**
 * @type {WysiwygConfigOptions}
 */
let options = {};

function isDrawing(node) {
    return node.hasAttribute('drawio-diagram');
}

function showDrawingManager(mceEditor, selectedNode = null) {
    pageEditor = mceEditor;
    currentNode = selectedNode;

    /** @type {ImageManager} * */
    const imageManager = window.$components.first('image-manager');
    imageManager.show(image => {
        if (selectedNode) {
            const imgElem = selectedNode.querySelector('img');
            pageEditor.undoManager.transact(() => {
                pageEditor.dom.setAttrib(imgElem, 'src', image.url);
                pageEditor.dom.setAttrib(selectedNode, 'drawio-diagram', image.id);
            });
        } else {
            const imgHTML = `<div drawio-diagram="${image.id}" contenteditable="false"><img src="${image.url}"></div>`;
            pageEditor.insertContent(imgHTML);
        }
    }, 'drawio');
}

async function updateContent(pngData) {
    const loadingImage = window.baseUrl('/loading.gif');

    const handleUploadError = error => {
        if (error.status === 413) {
            window.$events.emit('error', options.translations.serverUploadLimitText);
        } else {
            window.$events.emit('error', options.translations.imageUploadErrorText);
        }
        console.error(error);
    };

    // Handle updating an existing image
    if (currentNode) {
        DrawIO.close();
        const imgElem = currentNode.querySelector('img');
        try {
            const img = await DrawIO.upload(pngData, options.pageId);
            pageEditor.undoManager.transact(() => {
                pageEditor.dom.setAttrib(imgElem, 'src', img.url);
                pageEditor.dom.setAttrib(currentNode, 'drawio-diagram', img.id);
            });
        } catch (err) {
            handleUploadError(err);
            throw new Error(`Failed to save image with error: ${err}`);
        }
        return;
    }

    await wait(5);

    const id = `drawing-${Math.random().toString(16).slice(2)}`;
    const wrapId = `drawing-wrap-${Math.random().toString(16).slice(2)}`;
    pageEditor.insertContent(`<div drawio-diagram contenteditable="false" id="${wrapId}"><img src="${loadingImage}" id="${id}"></div>`);
    DrawIO.close();

    try {
        const img = await DrawIO.upload(pngData, options.pageId);
        pageEditor.undoManager.transact(() => {
            pageEditor.dom.setAttrib(id, 'src', img.url);
            pageEditor.dom.setAttrib(wrapId, 'drawio-diagram', img.id);
        });
    } catch (err) {
        pageEditor.dom.remove(wrapId);
        handleUploadError(err);
        throw new Error(`Failed to save image with error: ${err}`);
    }
}

function drawingInit() {
    if (!currentNode) {
        return Promise.resolve('');
    }

    const drawingId = currentNode.getAttribute('drawio-diagram');
    return DrawIO.load(drawingId);
}

function showDrawingEditor(mceEditor, selectedNode = null) {
    pageEditor = mceEditor;
    currentNode = selectedNode;
    DrawIO.show(options.drawioUrl, drawingInit, updateContent);
}

/**
 * @param {Editor} editor
 */
function register(editor) {
    editor.addCommand('drawio', () => {
        const selectedNode = editor.selection.getNode();
        showDrawingEditor(editor, isDrawing(selectedNode) ? selectedNode : null);
    });

    editor.ui.registry.addIcon('diagram', `<svg width="24" height="24" fill="${options.darkMode ? '#BBB' : '#000000'}" xmlns="http://www.w3.org/2000/svg"><path d="M20.716 7.639V2.845h-4.794v1.598h-7.99V2.845H3.138v4.794h1.598v7.99H3.138v4.794h4.794v-1.598h7.99v1.598h4.794v-4.794h-1.598v-7.99zM4.736 4.443h1.598V6.04H4.736zm1.598 14.382H4.736v-1.598h1.598zm9.588-1.598h-7.99v-1.598H6.334v-7.99h1.598V6.04h7.99v1.598h1.598v7.99h-1.598zm3.196 1.598H17.52v-1.598h1.598zM17.52 6.04V4.443h1.598V6.04zm-4.21 7.19h-2.79l-.582 1.599H8.643l2.717-7.191h1.119l2.724 7.19h-1.302zm-2.43-1.006h2.086l-1.039-3.06z"/></svg>`);

    editor.ui.registry.addSplitButton('drawio', {
        tooltip: 'Insert/edit drawing',
        icon: 'diagram',
        onAction() {
            editor.execCommand('drawio');
            // Hack to de-focus the tinymce editor toolbar
            window.document.body.dispatchEvent(new Event('mousedown', {bubbles: true}));
        },
        fetch(callback) {
            callback([
                {
                    type: 'choiceitem',
                    text: 'Drawing manager',
                    value: 'drawing-manager',
                },
            ]);
        },
        onItemAction(api, value) {
            if (value === 'drawing-manager') {
                const selectedNode = editor.selection.getNode();
                showDrawingManager(editor, isDrawing(selectedNode) ? selectedNode : null);
            }
        },
    });

    editor.on('dblclick', () => {
        const selectedNode = editor.selection.getNode();
        if (!isDrawing(selectedNode)) return;
        showDrawingEditor(editor, selectedNode);
    });

    editor.on('SetContent', () => {
        const drawings = editor.dom.select('body > div[drawio-diagram]');
        if (!drawings.length) return;

        editor.undoManager.transact(() => {
            for (const drawing of drawings) {
                drawing.setAttribute('contenteditable', 'false');
            }
        });
    });
}

/**
 *
 * @param {WysiwygConfigOptions} providedOptions
 * @return {function(Editor, string)}
 */
export function getPlugin(providedOptions) {
    options = providedOptions;
    return register;
}
