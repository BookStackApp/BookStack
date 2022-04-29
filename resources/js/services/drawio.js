let iFrame = null;
let lastApprovedOrigin;
let onInit, onSave;

/**
 * Show the draw.io editor.
 * @param {String} drawioUrl
 * @param {Function} onInitCallback - Must return a promise with the xml to load for the editor.
 * @param {Function} onSaveCallback - Is called with the drawing data on save.
 */
function show(drawioUrl, onInitCallback, onSaveCallback) {
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

function close() {
    drawEventClose();
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

function drawEventExport(message) {
    if (onSave) {
        onSave(message.data);
    }
}

function drawEventSave(message) {
    drawPostMessage({action: 'export', format: 'xmlpng', xml: message.xml, spin: 'Updating drawing'});
}

function drawEventInit() {
    if (!onInit) return;
    onInit().then(xml => {
        drawPostMessage({action: 'load', autosave: 1, xml: xml});
    });
}

function drawEventConfigure() {
    const config = {};
    window.$events.emitPublic(iFrame, 'editor-drawio::configure', {config});
    drawPostMessage({action: 'configure', config});
}

function drawEventClose() {
    window.removeEventListener('message', drawReceive);
    if (iFrame) document.body.removeChild(iFrame);
}

function drawPostMessage(data) {
    iFrame.contentWindow.postMessage(JSON.stringify(data), lastApprovedOrigin);
}

async function upload(imageData, pageUploadedToId) {
    let data = {
        image: imageData,
        uploaded_to: pageUploadedToId,
    };
    const resp = await window.$http.post(window.baseUrl(`/images/drawio`), data);
    return resp.data;
}

/**
 * Load an existing image, by fetching it as Base64 from the system.
 * @param drawingId
 * @returns {Promise<string>}
 */
async function load(drawingId) {
    const resp = await window.$http.get(window.baseUrl(`/images/drawio/base64/${drawingId}`));
    return `data:image/png;base64,${resp.data.content}`;
}

export default {show, close, upload, load};