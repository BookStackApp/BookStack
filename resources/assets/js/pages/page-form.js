"use strict";
const Code = require('../code');

/**
 * Handle pasting images from clipboard.
 * @param {ClipboardEvent} event
 * @param editor
 */
function editorPaste(event, editor) {
    if (!event.clipboardData || !event.clipboardData.items) return;
    let items = event.clipboardData.items;

    for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf("image") === -1) continue;
        event.preventDefault();

        let id = "image-" + Math.random().toString(16).slice(2);
        let loadingImage = window.baseUrl('/loading.gif');
        let file = items[i].getAsFile();
        setTimeout(() => {
            editor.insertContent(`<p><img src="${loadingImage}" id="${id}"></p>`);
            uploadImageFile(file).then(resp => {
                editor.dom.setAttrib(id, 'src', resp.thumbs.display);
            }).catch(err => {
                editor.dom.remove(id);
                window.$events.emit('error', trans('errors.image_upload_error'));
                console.log(err);
            });
        }, 10);
    }
}

/**
 * Upload an image file to the server
 * @param {File} file
 */
function uploadImageFile(file) {
    if (file === null || file.type.indexOf('image') !== 0) return Promise.reject(`Not an image file`);

    let ext = 'png';
    if (file.name) {
        let fileNameMatches = file.name.match(/\.(.+)$/);
        if (fileNameMatches.length > 1) ext = fileNameMatches[1];
    }

    let remoteFilename = "image-" + Date.now() + "." + ext;
    let formData = new FormData();
    formData.append('file', file, remoteFilename);

    return window.$http.post(window.baseUrl('/images/gallery/upload'), formData).then(resp => (resp.data));
}

function registerEditorShortcuts(editor) {
    // Headers
    for (let i = 1; i < 5; i++) {
        editor.shortcuts.add('meta+' + i, '', ['FormatBlock', false, 'h' + (i+1)]);
    }

    // Other block shortcuts
    editor.shortcuts.add('meta+5', '', ['FormatBlock', false, 'p']);
    editor.shortcuts.add('meta+d', '', ['FormatBlock', false, 'p']);
    editor.shortcuts.add('meta+6', '', ['FormatBlock', false, 'blockquote']);
    editor.shortcuts.add('meta+q', '', ['FormatBlock', false, 'blockquote']);
    editor.shortcuts.add('meta+7', '', ['codeeditor', false, 'pre']);
    editor.shortcuts.add('meta+e', '', ['codeeditor', false, 'pre']);
    editor.shortcuts.add('meta+8', '', ['FormatBlock', false, 'code']);
    editor.shortcuts.add('meta+shift+E', '', ['FormatBlock', false, 'code']);

    // Save draft shortcut
    editor.shortcuts.add('meta+S', '', () => {
        window.$events.emit('editor-save-draft');
    });

    // Save page shortcut
    editor.shortcuts.add('meta+13', '', () => {
        window.$events.emit('editor-save-page');
    });

    // Loop through callout styles
    editor.shortcuts.add('meta+9', '', function() {
        let selectedNode = editor.selection.getNode();
        let formats = ['info', 'success', 'warning', 'danger'];

        if (!selectedNode || selectedNode.className.indexOf('callout') === -1) {
            editor.formatter.apply('calloutinfo');
            return;
        }

        for (let i = 0; i < formats.length; i++) {
            if (selectedNode.className.indexOf(formats[i]) === -1) continue;
            let newFormat = (i === formats.length -1) ? formats[0] : formats[i+1];
            editor.formatter.apply('callout' + newFormat);
            return;
        }
        editor.formatter.apply('p');
    });

}

/**
 * Load custom HTML head content from the settings into the editor.
 * @param editor
 */
function loadCustomHeadContent(editor) {
    window.$http.get(window.baseUrl('/custom-head-content')).then(resp => {
        if (!resp.data) return;
        let head = editor.getDoc().querySelector('head');
        head.innerHTML += resp.data;
    });
}

/**
 * Create and enable our custom code plugin
 */
function codePlugin() {

    function elemIsCodeBlock(elem) {
        return elem.className === 'CodeMirrorContainer';
    }

    function showPopup(editor) {
        let selectedNode = editor.selection.getNode();

        if (!elemIsCodeBlock(selectedNode)) {
            let providedCode = editor.selection.getNode().textContent;
            window.vues['code-editor'].open(providedCode, '', (code, lang) => {
                let wrap = document.createElement('div');
                wrap.innerHTML = `<pre><code class="language-${lang}"></code></pre>`;
                wrap.querySelector('code').innerText = code;

                editor.formatter.toggle('pre');
                let node = editor.selection.getNode();
                editor.dom.setHTML(node, wrap.querySelector('pre').innerHTML);
                editor.fire('SetContent');
            });
            return;
        }

        let lang = selectedNode.hasAttribute('data-lang') ? selectedNode.getAttribute('data-lang') : '';
        let currentCode = selectedNode.querySelector('textarea').textContent;

        window.vues['code-editor'].open(currentCode, lang, (code, lang) => {
            let editorElem = selectedNode.querySelector('.CodeMirror');
            let cmInstance = editorElem.CodeMirror;
            if (cmInstance) {
                Code.setContent(cmInstance, code);
                Code.setMode(cmInstance, lang);
            }
            let textArea = selectedNode.querySelector('textarea');
            if (textArea) textArea.textContent = code;
            selectedNode.setAttribute('data-lang', lang);
        });
    }

    function codeMirrorContainerToPre($codeMirrorContainer) {
        let textArea = $codeMirrorContainer[0].querySelector('textarea');
        let code = textArea.textContent;
        let lang = $codeMirrorContainer[0].getAttribute('data-lang');

        $codeMirrorContainer.removeAttr('contentEditable');
        let $pre = $('<pre></pre>');
        $pre.append($('<code></code>').each((index, elem) => {
            // Needs to be textContent since innerText produces BR:s
            elem.textContent = code;
        }).attr('class', `language-${lang}`));
        $codeMirrorContainer.replaceWith($pre);
    }

    window.tinymce.PluginManager.add('codeeditor', function(editor, url) {

        let $ = editor.$;

        editor.addButton('codeeditor', {
            text: 'Code block',
            icon: false,
            cmd: 'codeeditor'
        });

        editor.addCommand('codeeditor', () => {
            showPopup(editor);
        });

        // Convert
        editor.on('PreProcess', function (e) {
            $('div.CodeMirrorContainer', e.node).
            each((index, elem) => {
                let $elem = $(elem);
                codeMirrorContainerToPre($elem);
            });
        });

        editor.on('dblclick', event => {
            let selectedNode = editor.selection.getNode();
            if (!elemIsCodeBlock(selectedNode)) return;
            showPopup(editor);
        });

        editor.on('SetContent', function () {

            // Recover broken codemirror instances
            $('.CodeMirrorContainer').filter((index ,elem) => {
                return typeof elem.querySelector('.CodeMirror').CodeMirror === 'undefined';
            }).each((index, elem) => {
                codeMirrorContainerToPre($(elem));
            });

            let codeSamples = $('body > pre').filter((index, elem) => {
                return elem.contentEditable !== "false";
            });

            if (!codeSamples.length) return;
            editor.undoManager.transact(function () {
                codeSamples.each((index, elem) => {
                    Code.wysiwygView(elem);
                });
            });
        });

    });
}
codePlugin();

function drawIoPlugin() {

    const drawIoUrl = 'https://www.draw.io/?embed=1&ui=atlas&spin=1&proto=json';
    let iframe = null;
    let pageEditor = null;
    let currentNode = null;

    function isDrawing(node) {
        return node.hasAttribute('drawio-diagram');
    }

    function showDrawingEditor(mceEditor, selectedNode = null) {
        pageEditor = mceEditor;
        currentNode = selectedNode;
        iframe = document.createElement('iframe');
        iframe.setAttribute('frameborder', '0');
        window.addEventListener('message', drawReceive);
        iframe.setAttribute('src', drawIoUrl);
        iframe.setAttribute('class', 'fullscreen');
        iframe.style.backgroundColor = '#FFFFFF';
        document.body.appendChild(iframe);
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

    function updateContent(pngData) {
        let id = "image-" + Math.random().toString(16).slice(2);
        let loadingImage = window.baseUrl('/loading.gif');
        let data = {
            image: pngData,
            uploaded_to: Number(document.getElementById('page-editor').getAttribute('page-id'))
        };

        // TODO - Handle updating an existing image

        setTimeout(() => {
            pageEditor.insertContent(`<div drawio-diagram contenteditable="false"><img src="${loadingImage}" id="${id}"></div>`);
            drawEventClose();
            window.$http.post(window.baseUrl('/images/drawing/upload'), data).then(resp => {
                pageEditor.dom.setAttrib(id, 'src', resp.data.url);
                pageEditor.dom.get(id).parentNode.setAttribute('drawio-diagram', resp.data.id);
            }).catch(err => {
                pageEditor.dom.remove(id);
                window.$events.emit('error', trans('errors.image_upload_error'));
                console.log(err);
            });
        }, 5);
    }

    function drawEventExport(message) {
        updateContent(message.data);
    }

    function drawEventSave(message) {
        drawPostMessage({action: 'export', format: 'xmlpng', xml: message.xml, spin: 'Updating drawing'});
    }

    function drawEventInit() {
        if (!currentNode) {
            drawPostMessage({action: 'load', autosave: 1, xml: ''});
            return;
        }

        let imgElem = currentNode.querySelector('img');
        let drawingId = currentNode.getAttribute('drawio-diagram');
        $http.get(window.baseUrl(`/images/base64/${drawingId}`)).then(resp => {
            let xml = `data:image/png;base64,${resp.data.content}`;
            drawPostMessage({action: 'load', autosave: 1, xml});
        });
    }

    function drawEventClose() {
        window.removeEventListener('message', drawReceive);
        if (iframe) document.body.removeChild(iframe);
    }

    function drawPostMessage(data) {
        iframe.contentWindow.postMessage(JSON.stringify(data), '*');
    }

    window.tinymce.PluginManager.add('drawio', function(editor, url) {

        let $ = editor.$;

        editor.addCommand('drawio', () => {
            showDrawingEditor(editor);
        });

        editor.addButton('drawio', {
            text: 'Drawing',
            icon: false,
            cmd: 'drawio'
        });

        editor.on('dblclick', event => {
            let selectedNode = editor.selection.getNode();
            if (!isDrawing(selectedNode)) return;
            showDrawingEditor(editor, selectedNode);
        });

        editor.on('SetContent', function () {
            let drawings = $('body > div[drawio-diagram]');
            if (!drawings.length) return;

            editor.undoManager.transact(function () {
                drawings.each((index, elem) => {
                    elem.setAttribute('contenteditable', 'false');
                });
            });
        });

    });
}
drawIoPlugin();

window.tinymce.PluginManager.add('customhr', function (editor) {
    editor.addCommand('InsertHorizontalRule', function () {
        let hrElem = document.createElement('hr');
        let cNode = editor.selection.getNode();
        let parentNode = cNode.parentNode;
        parentNode.insertBefore(hrElem, cNode);
    });

    editor.addButton('hr', {
        icon: 'hr',
        tooltip: 'Horizontal line',
        cmd: 'InsertHorizontalRule'
    });

    editor.addMenuItem('hr', {
        icon: 'hr',
        text: 'Horizontal line',
        cmd: 'InsertHorizontalRule',
        context: 'insert'
    });
});



module.exports = {
    selector: '#html-editor',
    content_css: [
        window.baseUrl('/css/styles.css'),
        window.baseUrl('/libs/material-design-iconic-font/css/material-design-iconic-font.min.css')
    ],
    branding: false,
    body_class: 'page-content',
    browser_spellcheck: true,
    relative_urls: false,
    remove_script_host: false,
    document_base_url: window.baseUrl('/'),
    statusbar: false,
    menubar: false,
    paste_data_images: false,
    extended_valid_elements: 'pre[*],svg[*],div[drawio-diagram]',
    automatic_uploads: false,
    valid_children: "-div[p|h1|h2|h3|h4|h5|h6|blockquote],+div[pre],+div[img]",
    plugins: "image table textcolor paste link autolink fullscreen imagetools code customhr autosave lists codeeditor drawio",
    imagetools_toolbar: 'imageoptions',
    toolbar: "undo redo | styleselect | bold italic underline strikethrough superscript subscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image-insert link hr | removeformat code fullscreen drawio",
    content_style: "body {padding-left: 15px !important; padding-right: 15px !important; margin:0!important; margin-left:auto!important;margin-right:auto!important;}",
    style_formats: [
        {title: "Header Large", format: "h2"},
        {title: "Header Medium", format: "h3"},
        {title: "Header Small", format: "h4"},
        {title: "Header Tiny", format: "h5"},
        {title: "Paragraph", format: "p", exact: true, classes: ''},
        {title: "Blockquote", format: "blockquote"},
        {title: "Code Block", icon: "code", cmd: 'codeeditor', format: 'codeeditor'},
        {title: "Inline Code", icon: "code", inline: "code"},
        {title: "Callouts", items: [
            {title: "Info", format: 'calloutinfo'},
            {title: "Success", format: 'calloutsuccess'},
            {title: "Warning", format: 'calloutwarning'},
            {title: "Danger", format: 'calloutdanger'}
        ]},
    ],
    style_formats_merge: false,
    formats: {
        codeeditor: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div'},
        alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-left'},
        aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-center'},
        alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-right'},
        calloutsuccess: {block: 'p', exact: true, attributes: {class: 'callout success'}},
        calloutinfo: {block: 'p', exact: true, attributes: {class: 'callout info'}},
        calloutwarning: {block: 'p', exact: true, attributes: {class: 'callout warning'}},
        calloutdanger: {block: 'p', exact: true, attributes: {class: 'callout danger'}}
    },
    file_browser_callback: function (field_name, url, type, win) {

        if (type === 'file') {
            window.EntitySelectorPopup.show(function(entity) {
                let originalField = win.document.getElementById(field_name);
                originalField.value = entity.link;
                $(originalField).closest('.mce-form').find('input').eq(2).val(entity.name);
            });
        }

        if (type === 'image') {
            // Show image manager
            window.ImageManager.show(function (image) {

                // Set popover link input to image url then fire change event
                // to ensure the new value sticks
                win.document.getElementById(field_name).value = image.url;
                if ("createEvent" in document) {
                    let evt = document.createEvent("HTMLEvents");
                    evt.initEvent("change", false, true);
                    win.document.getElementById(field_name).dispatchEvent(evt);
                } else {
                    win.document.getElementById(field_name).fireEvent("onchange");
                }

                // Replace the actively selected content with the linked image
                let html = `<a href="${image.url}" target="_blank">`;
                html += `<img src="${image.thumbs.display}" alt="${image.name}">`;
                html += '</a>';
                win.tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
            });
        }

    },
    paste_preprocess: function (plugin, args) {
        let content = args.content;
        if (content.indexOf('<img src="file://') !== -1) {
            args.content = '';
        }
    },
    init_instance_callback: function(editor) {
        loadCustomHeadContent(editor);
    },
    setup: function (editor) {

        editor.on('init ExecCommand change input NodeChange ObjectResized', editorChange);

        function editorChange() {
            let content = editor.getContent();
            window.$events.emit('editor-html-change', content);
        }

        window.$events.listen('editor-html-update', html => {
            editor.setContent(html);
            editor.selection.select(editor.getBody(), true);
            editor.selection.collapse(false);
            editorChange(html);
        });

        registerEditorShortcuts(editor);

        let wrap;

        function hasTextContent(node) {
            return node && !!( node.textContent || node.innerText );
        }

        editor.on('dragstart', function () {
            let node = editor.selection.getNode();

            if (node.nodeName !== 'IMG') return;
            wrap = editor.dom.getParent(node, '.mceTemp');

            if (!wrap && node.parentNode.nodeName === 'A' && !hasTextContent(node.parentNode)) {
                wrap = node.parentNode;
            }
        });

        editor.on('drop', function (event) {
            let dom = editor.dom,
                rng = tinymce.dom.RangeUtils.getCaretRangeFromPoint(event.clientX, event.clientY, editor.getDoc());

            // Don't allow anything to be dropped in a captioned image.
            if (dom.getParent(rng.startContainer, '.mceTemp')) {
                event.preventDefault();
            } else if (wrap) {
                event.preventDefault();

                editor.undoManager.transact(function () {
                    editor.selection.setRng(rng);
                    editor.selection.setNode(wrap);
                    dom.remove(wrap);
                });
            }

            wrap = null;
        });

        // Custom Image picker button
        editor.addButton('image-insert', {
            title: 'My title',
            icon: 'image',
            tooltip: 'Insert an image',
            onclick: function () {
                window.ImageManager.show(function (image) {
                    let html = `<a href="${image.url}" target="_blank">`;
                    html += `<img src="${image.thumbs.display}" alt="${image.name}">`;
                    html += '</a>';
                    editor.execCommand('mceInsertContent', false, html);
                });
            }
        });

        // Paste image-uploads
        editor.on('paste', event => { editorPaste(event, editor) });
    }
};