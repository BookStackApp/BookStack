import {Clipboard} from '../services/clipboard';

let wrap;
let draggedContentEditable;

function hasTextContent(node) {
    return node && !!(node.textContent || node.innerText);
}

/**
 * Upload an image file to the server
 * @param {File} file
 * @param {int} pageId
 */
async function uploadImageFile(file, pageId) {
    if (file === null || file.type.indexOf('image') !== 0) {
        throw new Error('Not an image file');
    }

    const remoteFilename = file.name || `image-${Date.now()}.png`;
    const formData = new FormData();
    formData.append('file', file, remoteFilename);
    formData.append('uploaded_to', pageId);

    const resp = await window.$http.post(window.baseUrl('/images/gallery'), formData);
    return resp.data;
}

/**
 * Handle pasting images from clipboard.
 * @param {Editor} editor
 * @param {WysiwygConfigOptions} options
 * @param {ClipboardEvent|DragEvent} event
 */
function paste(editor, options, event) {
    const clipboard = new Clipboard(event.clipboardData || event.dataTransfer);

    // Don't handle the event ourselves if no items exist of contains table-looking data
    if (!clipboard.hasItems() || clipboard.containsTabularData()) {
        return;
    }

    const images = clipboard.getImages();
    for (const imageFile of images) {
        const id = `image-${Math.random().toString(16).slice(2)}`;
        const loadingImage = window.baseUrl('/loading.gif');
        event.preventDefault();

        setTimeout(() => {
            editor.insertContent(`<p><img src="${loadingImage}" id="${id}"></p>`);

            uploadImageFile(imageFile, options.pageId).then(resp => {
                const safeName = resp.name.replace(/"/g, '');
                const newImageHtml = `<img src="${resp.thumbs.display}" alt="${safeName}" />`;

                const newEl = editor.dom.create('a', {
                    target: '_blank',
                    href: resp.url,
                }, newImageHtml);

                editor.dom.replace(newEl, id);
            }).catch(err => {
                editor.dom.remove(id);
                window.$events.error(err?.data?.message || options.translations.imageUploadErrorText);
                console.error(err);
            });
        }, 10);
    }
}

/**
 * @param {Editor} editor
 */
function dragStart(editor) {
    const node = editor.selection.getNode();

    if (node.nodeName === 'IMG') {
        wrap = editor.dom.getParent(node, '.mceTemp');

        if (!wrap && node.parentNode.nodeName === 'A' && !hasTextContent(node.parentNode)) {
            wrap = node.parentNode;
        }
    }

    // Track dragged contenteditable blocks
    if (node.hasAttribute('contenteditable') && node.getAttribute('contenteditable') === 'false') {
        draggedContentEditable = node;
    }
}

/**
 * @param {Editor} editor
 * @param {WysiwygConfigOptions} options
 * @param {DragEvent} event
 */
function drop(editor, options, event) {
    const {dom} = editor;
    const rng = window.tinymce.dom.RangeUtils.getCaretRangeFromPoint(
        event.clientX,
        event.clientY,
        editor.getDoc(),
    );

    // Template insertion
    const templateId = event.dataTransfer && event.dataTransfer.getData('bookstack/template');
    if (templateId) {
        event.preventDefault();
        window.$http.get(`/templates/${templateId}`).then(resp => {
            editor.selection.setRng(rng);
            editor.undoManager.transact(() => {
                editor.execCommand('mceInsertContent', false, resp.data.html);
            });
        });
    }

    // Don't allow anything to be dropped in a captioned image.
    if (dom.getParent(rng.startContainer, '.mceTemp')) {
        event.preventDefault();
    } else if (wrap) {
        event.preventDefault();

        editor.undoManager.transact(() => {
            editor.selection.setRng(rng);
            editor.selection.setNode(wrap);
            dom.remove(wrap);
        });
    }

    // Handle contenteditable section drop
    if (!event.isDefaultPrevented() && draggedContentEditable) {
        event.preventDefault();
        editor.undoManager.transact(() => {
            const selectedNode = editor.selection.getNode();
            const range = editor.selection.getRng();
            const selectedNodeRoot = selectedNode.closest('body > *');
            if (range.startOffset > (range.startContainer.length / 2)) {
                selectedNodeRoot.after(draggedContentEditable);
            } else {
                selectedNodeRoot.before(draggedContentEditable);
            }
        });
    }

    // Handle image insert
    if (!event.isDefaultPrevented()) {
        paste(editor, options, event);
    }

    wrap = null;
}

/**
 * @param {Editor} editor
 * @param {WysiwygConfigOptions} options
 */
export function listenForDragAndPaste(editor, options) {
    editor.on('dragstart', () => dragStart(editor));
    editor.on('drop', event => drop(editor, options, event));
    editor.on('paste', event => paste(editor, options, event));
}
