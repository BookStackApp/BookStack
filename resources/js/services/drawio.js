// Docs: https://www.diagrams.net/doc/faq/embed-mode

let iFrame = null;
let lastApprovedOrigin;
let onInit; let
    onSave;

function drawPostMessage(data) {
    iFrame.contentWindow.postMessage(JSON.stringify(data), lastApprovedOrigin);
}

function drawEventExport(message) {
    if (onSave) {
        onSave(message.data);
    }
}

function drawEventSave(message) {
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
 * Show the draw.io editor.
 * @param {String} drawioUrl
 * @param {Function} onInitCallback - Must return a promise with the xml to load for the editor.
 * @param {Function} onSaveCallback - Is called with the drawing data on save.
 */
export function show(drawioUrl, onInitCallback, onSaveCallback) {
    onInit = onInitCallback;
    onSave = onSaveCallback;

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
