"use strict";

const Code = require('../code');

/**
 * Handle pasting images from clipboard.
 * @param e  - event
 * @param editor - editor instance
 */
function editorPaste(e, editor) {
    if (!e.clipboardData) return;
    let items = e.clipboardData.items;
    if (!items) return;
    for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf("image") === -1) return;

        let file = items[i].getAsFile();
        let formData = new FormData();
        let ext = 'png';
        let xhr = new XMLHttpRequest();

        if (file.name) {
            let fileNameMatches = file.name.match(/\.(.+)$/);
            if (fileNameMatches) {
                ext = fileNameMatches[1];
            }
        }

        let id = "image-" + Math.random().toString(16).slice(2);
        let loadingImage = window.baseUrl('/loading.gif');
        editor.execCommand('mceInsertContent', false, `<img src="${loadingImage}" id="${id}">`);

        let remoteFilename = "image-" + Date.now() + "." + ext;
        formData.append('file', file, remoteFilename);
        formData.append('_token', document.querySelector('meta[name="token"]').getAttribute('content'));

        xhr.open('POST', window.baseUrl('/images/gallery/upload'));
        xhr.onload = function () {
            if (xhr.status === 200 || xhr.status === 201) {
                let result = JSON.parse(xhr.responseText);
                editor.dom.setAttrib(id, 'src', result.thumbs.display);
            } else {
                console.log('An error occurred uploading the image', xhr.responseText);
                editor.dom.remove(id);
            }
        };
        xhr.send(formData);
        
    }
}

function registerEditorShortcuts(editor) {
    // Headers
    for (let i = 1; i < 5; i++) {
        editor.addShortcut('meta+' + i, '', ['FormatBlock', false, 'h' + i]);
    }

    // Other block shortcuts
    editor.addShortcut('meta+q', '', ['FormatBlock', false, 'blockquote']);
    editor.addShortcut('meta+d', '', ['FormatBlock', false, 'p']);
    editor.addShortcut('meta+e', '', ['codeeditor', false, 'pre']);
    editor.addShortcut('meta+shift+E', '', ['FormatBlock', false, 'code']);
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

    window.tinymce.PluginManager.add('codeeditor', (editor, url) => {

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

module.exports = function() {
    codePlugin();
    let settings = {
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
        extended_valid_elements: 'pre[*]',
        automatic_uploads: false,
        valid_children: "-div[p|h1|h2|h3|h4|h5|h6|blockquote],+div[pre]",
        plugins: "image table textcolor paste link autolink fullscreen imagetools code customhr autosave lists codeeditor",
        imagetools_toolbar: 'imageoptions',
        toolbar: "undo redo | styleselect | bold italic underline strikethrough superscript subscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image-insert link hr | removeformat code fullscreen",
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
                {title: "Success", block: 'p', exact: true, attributes : {'class' : 'callout success'}},
                {title: "Info", block: 'p', exact: true, attributes : {'class' : 'callout info'}},
                {title: "Warning", block: 'p', exact: true, attributes : {'class' : 'callout warning'}},
                {title: "Danger", block: 'p', exact: true, attributes : {'class' : 'callout danger'}}
            ]},
        ],
        style_formats_merge: false,
        formats: {
            codeeditor: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div'},
            alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-left'},
            aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-center'},
            alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-right'},
        },
        file_browser_callback: function (field_name, url, type, win) {

            if (type === 'file') {
                window.showEntityLinkSelector(function(entity) {
                    let originalField = win.document.getElementById(field_name);
                    originalField.value = entity.link;
                    $(originalField).closest('.mce-form').find('input').eq(2).val(entity.name);
                });
            }

            if (type === 'image') {
                // Show image manager
                window.ImageManager.showExternal(function (image) {

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
        extraSetups: [],
        setup: function (editor) {

            // Run additional setup actions
            // Used by the angular side of things
            for (let i = 0; i < settings.extraSetups.length; i++) {
                settings.extraSetups[i](editor);
            }

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
                    window.ImageManager.showExternal(function (image) {
                        let html = `<a href="${image.url}" target="_blank">`;
                        html += `<img src="${image.thumbs.display}" alt="${image.name}">`;
                        html += '</a>';
                        editor.execCommand('mceInsertContent', false, html);
                    });
                }
            });

            // Paste image-uploads
            editor.on('paste', function(event) {
                editorPaste(event, editor);
            });
        }
    };
    return settings;
};