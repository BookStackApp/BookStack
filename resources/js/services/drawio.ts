// Docs: https://www.diagrams.net/doc/faq/embed-mode
import * as store from './store';
import {ConfirmDialog} from "../components";
import {HttpError} from "./http";

type DrawioExportEventResponse = {
    action: 'export',
    format: string,
    message: string,
    data: string,
    xml: string,
};

type DrawioSaveEventResponse = {
    action: 'save',
    xml: string,
};

let iFrame: HTMLIFrameElement|null = null;
let lastApprovedOrigin: string;
let onInit: () => Promise<string>;
let onSave: (data: string) => Promise<any>;
const saveBackupKey = 'last-drawing-save';

function drawPostMessage(data: Record<any, any>): void {
    iFrame?.contentWindow?.postMessage(JSON.stringify(data), lastApprovedOrigin);
}

function drawEventExport(message: DrawioExportEventResponse) {
    store.set(saveBackupKey, message.data);
    if (onSave) {
        onSave(message.data).then(() => {
            store.del(saveBackupKey);
        });
    }
}

function drawEventSave(message: DrawioSaveEventResponse) {
    drawPostMessage({
        action: 'export', format: 'xmlpng', xml: message.xml, spin: 'Updating drawing',
    });
}

function drawEventInit() {
    if (!onInit) return;
    onInit().then(xml => {
        drawPostMessage({action: 'load', autosave: 1, xml});
    });
}

function drawEventConfigure() {
    const config = {};
    if (iFrame) {
        window.$events.emitPublic(iFrame, 'editor-drawio::configure', {config});
        drawPostMessage({action: 'configure', config});
    }
}

function drawEventClose() {
    // eslint-disable-next-line no-use-before-define
    window.removeEventListener('message', drawReceive);
    if (iFrame) document.body.removeChild(iFrame);
}

/**
 * Receive and handle a message event from the draw.io window.
 */
function drawReceive(event: MessageEvent) {
    if (!event.data || event.data.length < 1) return;
    if (event.origin !== lastApprovedOrigin) return;

    const message = JSON.parse(event.data);
    if (message.event === 'init') {
        drawEventInit();
    } else if (message.event === 'exit') {
        drawEventClose();
    } else if (message.event === 'save') {
        drawEventSave(message as DrawioSaveEventResponse);
    } else if (message.event === 'export') {
        drawEventExport(message as DrawioExportEventResponse);
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

    if (backupVal && dialogEl) {
        const dialog = window.$components.firstOnElement(dialogEl, 'confirm-dialog') as ConfirmDialog;
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
 * onSaveCallback Is called with the drawing data on save.
 */
export async function show(drawioUrl: string, onInitCallback: () => Promise<string>, onSaveCallback: (data: string) => Promise<void>): Promise<void> {
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

export async function upload(imageData: string, pageUploadedToId: string): Promise<{id: number, url: string}> {
    const data = {
        image: imageData,
        uploaded_to: pageUploadedToId,
    };
    const resp = await window.$http.post(window.baseUrl('/images/drawio'), data);
    return resp.data as {id: number, url: string};
}

export function close() {
    drawEventClose();
}

/**
 * Load an existing image, by fetching it as Base64 from the system.
 */
export async function load(drawingId: string): Promise<string> {
    try {
        const resp = await window.$http.get(window.baseUrl(`/images/drawio/base64/${drawingId}`));
        const data = resp.data as {content: string};
        return `data:image/png;base64,${data.content}`;
    } catch (error) {
        if (error instanceof HttpError) {
            window.$events.showResponseError(error);
        }
        close();
        throw error;
    }
}
