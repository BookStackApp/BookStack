
const drawIoUrl = 'https://www.draw.io/?embed=1&ui=atlas&spin=1&proto=json';
let iFrame = null;

let onInit, onSave;

/**
 * Show the draw.io editor.
 * @param onInitCallback - Must return a promise with the xml to load for the editor.
 * @param onSaveCallback - Is called with the drawing data on save.
 */
function show(onInitCallback, onSaveCallback) {
    onInit = onInitCallback;
    onSave = onSaveCallback;

    iFrame = document.createElement('iframe');
    iFrame.setAttribute('frameborder', '0');
    window.addEventListener('message', drawReceive);
    iFrame.setAttribute('src', drawIoUrl);
    iFrame.setAttribute('class', 'fullscreen');
    iFrame.style.backgroundColor = '#FFFFFF';
    document.body.appendChild(iFrame);
}

function close() {
    drawEventClose();
}

function drawReceive(event) {
    if (!event.data || event.data.length < 1) return;
    let message = JSON.parse(event.data);
    if (message.event === 'init') {
        drawEventInit();
    } else if (message.event === 'exit') {
        drawEventClose();
    } else if (message.event === 'save') {
        drawEventSave(message);
    } else if (message.event === 'export') {
        drawEventExport(message);
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

function drawEventClose() {
    window.removeEventListener('message', drawReceive);
    if (iFrame) document.body.removeChild(iFrame);
}

function drawPostMessage(data) {
    iFrame.contentWindow.postMessage(JSON.stringify(data), '*');
}

export default {show, close};