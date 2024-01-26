// Docs: https://www.diagrams.net/doc/faq/embed-mode
import * as store from './store';

let iFrame = null;
let lastApprovedOrigin;
let onInit;
let onSave;
const saveBackupKey = 'last-drawing-save';

function drawPostMessage(data) {
    iFrame.contentWindow.postMessage(JSON.stringify(data), lastApprovedOrigin);
}

function drawEventExport(message) {
    store.set(saveBackupKey, message.data);
    if (onSave) {
        onSave(message.data).then(() => {
            store.del(saveBackupKey);
        });
    }
}

function drawEventSave(message) {
    const config = {
        format: 'xmlpng', xml: message.xml, spin: 'Updating drawing',
    };
    window.$events.emitPublic(iFrame, 'editor-drawio::export', {config});
    drawPostMessage({action: 'export', ...config});
}

function drawEventInit() {
    if (!onInit) return;
    onInit().then(xml => {
        drawPostMessage({action: 'load', autosave: 1, xml});
    });
}

function drawEventConfigure() {
    const config = {};
    window.$events.emitPublic(iFrame, 'editor-drawio::configure', {config});
    drawPostMessage({action: 'configure', config});
}

function drawEventClose() {
    // eslint-disable-next-line no-use-before-define
    window.removeEventListener('message', drawReceive);
    if (iFrame) document.body.removeChild(iFrame);
}

/**
 * Receive and handle a message event from the draw.io window.
 * @param {MessageEvent} event
 */
function drawReceive(event) {
    if (!event.data || event.data.length < 1) return;
    if (event.origin !== lastApprovedOrigin) return;

    const message = JSON.parse(event.data);
    if (message.event === 'init') {
        drawEventInit();
    } else if (message.event === 'exit') {
        drawEventClose();
    } else if (message.event === 'save') {
        drawEventSave(message);
    } else if (message.event === 'export') {
        drawEventExport(message);
    } else if (message.event === 'configure') {
        drawEventConfigure();
    }
}

/**
 * Attempt to prompt and restore unsaved drawing content if existing.
 * @returns {Promise<void>}
 */
async function attemptRestoreIfExists() {
    const backupVal = await store.get(saveBackupKey);
    const dialogEl = document.getElementById('unsaved-drawing-dialog');

    if (!dialogEl) {
        console.error('Missing expected unsaved-drawing dialog');
    }

    if (backupVal) {
        /** @var {ConfirmDialog} */
        const dialog = window.$components.firstOnElement(dialogEl, 'confirm-dialog');
        const restore = await dialog.show();
        if (restore) {
            onInit = async () => backupVal;
        }
    }
}

/**
 * Show the draw.io editor.
 * onSaveCallback must return a promise that resolves on successful save and errors on failure.
 * onInitCallback must return a promise with the xml to load for the editor.
 * Will attempt to provide an option to restore unsaved changes if found to exist.
 * @param {String} drawioUrl
 * @param {Function<Promise<String>>} onInitCallback
 * @param {Function<Promise>} onSaveCallback - Is called with the drawing data on save.
 */
export async function show(drawioUrl, onInitCallback, onSaveCallback) {
    onInit = onInitCallback;
    onSave = onSaveCallback;

    await attemptRestoreIfExists();

    iFrame = document.createElement('iframe');
    iFrame.setAttribute('frameborder', '0');
    window.addEventListener('message', drawReceive);
    iFrame.setAttribute('src', drawioUrl);
    iFrame.setAttribute('class', 'fullscreen');
    iFrame.style.backgroundColor = '#FFFFFF';
    document.body.appendChild(iFrame);
    lastApprovedOrigin = (new URL(drawioUrl)).origin;
}

export async function upload(imageData, pageUploadedToId) {
    const data = {
        image: imageData,
        uploaded_to: pageUploadedToId,
    };
    const resp = await window.$http.post(window.baseUrl('/images/drawio'), data);
    return resp.data;
}

export function close() {
    drawEventClose();
}

/**
 * Load an existing image, by fetching it as Base64 from the system.
 * @param drawingId
 * @returns {Promise<string>}
 */
export async function load(drawingId) {
    try {
        const resp = await window.$http.get(window.baseUrl(`/images/drawio/base64/${drawingId}`));
        return `data:image/png;base64,${resp.data.content}`;
    } catch (error) {
        if (error instanceof window.$http.HttpError) {
            window.$events.showResponseError(error);
        }
        close();
        throw error;
    }
}
