const Code = require('../services/code');
const DrawIO = require('../services/drawio');

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

function drawIoPlugin() {

    let pageEditor = null;
    let currentNode = null;

    function isDrawing(node) {
        return node.hasAttribute('drawio-diagram');
    }

    function showDrawingManager(mceEditor, selectedNode = null) {
        pageEditor = mceEditor;
        currentNode = selectedNode;
        // Show image manager
        window.ImageManager.show(function (image) {
            if (selectedNode) {
                let imgElem = selectedNode.querySelector('img');
                pageEditor.dom.setAttrib(imgElem, 'src', image.url);
                pageEditor.dom.setAttrib(selectedNode, 'drawio-diagram', image.id);
            } else {
                let imgHTML = `<div drawio-diagram="${image.id}" contenteditable="false"><img src="${image.url}"></div>`;
                pageEditor.insertContent(imgHTML);
            }
        }, 'drawio');
    }

    function showDrawingEditor(mceEditor, selectedNode = null) {
        pageEditor = mceEditor;
        currentNode = selectedNode;
        DrawIO.show(drawingInit, updateContent);
    }

    function updateContent(pngData) {
        let id = "image-" + Math.random().toString(16).slice(2);
        let loadingImage = window.baseUrl('/loading.gif');
        let data = {
            image: pngData,
            uploaded_to: Number(document.getElementById('page-editor').getAttribute('page-id'))
        };

        // Handle updating an existing image
        if (currentNode) {
            DrawIO.close();
            let imgElem = currentNode.querySelector('img');
            window.$http.post(window.baseUrl(`/images/drawing/upload`), data).then(resp => {
                pageEditor.dom.setAttrib(imgElem, 'src', resp.data.url);
                pageEditor.dom.setAttrib(currentNode, 'drawio-diagram', resp.data.id);
            }).catch(err => {
                window.$events.emit('error', trans('errors.image_upload_error'));
                console.log(err);
            });
            return;
        }

        setTimeout(() => {
            pageEditor.insertContent(`<div drawio-diagram contenteditable="false"><img src="${loadingImage}" id="${id}"></div>`);
            DrawIO.close();
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


    function drawingInit() {
        if (!currentNode) {
            return Promise.resolve('');
        }

        let drawingId = currentNode.getAttribute('drawio-diagram');
        return window.$http.get(window.baseUrl(`/images/base64/${drawingId}`)).then(resp => {
            return `data:image/png;base64,${resp.data.content}`;
        });
    }

    window.tinymce.PluginManager.add('drawio', function(editor, url) {

        editor.addCommand('drawio', () => {
            let selectedNode = editor.selection.getNode();
            showDrawingEditor(editor, isDrawing(selectedNode) ? selectedNode : null);
        });

        editor.addButton('drawio', {
            type: 'splitbutton',
            tooltip: 'Drawing',
            image: `data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9IiMwMDAwMDAiICB4bWxucz0iaHR0cDovL3d3 dy53My5vcmcvMjAwMC9zdmciPgogICAgPHBhdGggZD0iTTIzIDdWMWgtNnYySDdWMUgxdjZoMnYx MEgxdjZoNnYtMmgxMHYyaDZ2LTZoLTJWN2gyek0zIDNoMnYySDNWM3ptMiAxOEgzdi0yaDJ2Mnpt MTItMkg3di0ySDVWN2gyVjVoMTB2MmgydjEwaC0ydjJ6bTQgMmgtMnYtMmgydjJ6TTE5IDVWM2gy djJoLTJ6bS01LjI3IDloLTMuNDlsLS43MyAySDcuODlsMy40LTloMS40bDMuNDEgOWgtMS42M2wt Ljc0LTJ6bS0zLjA0LTEuMjZoMi42MUwxMiA4LjkxbC0xLjMxIDMuODN6Ii8+CiAgICA8cGF0aCBk PSJNMCAwaDI0djI0SDB6IiBmaWxsPSJub25lIi8+Cjwvc3ZnPg==`,
            cmd: 'drawio',
            menu: [
                {
                    text: 'Drawing Manager',
                    onclick() {
                        let selectedNode = editor.selection.getNode();
                        showDrawingManager(editor, isDrawing(selectedNode) ? selectedNode : null);
                    }
                }
            ]
        });

        editor.on('dblclick', event => {
            let selectedNode = editor.selection.getNode();
            if (!isDrawing(selectedNode)) return;
            showDrawingEditor(editor, selectedNode);
        });

        editor.on('SetContent', function () {
            let drawings = editor.$('body > div[drawio-diagram]');
            if (!drawings.length) return;

            editor.undoManager.transact(function () {
                drawings.each((index, elem) => {
                    elem.setAttribute('contenteditable', 'false');
                });
            });
        });

    });
}

function customHrPlugin() {
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
}


class WysiwygEditor {

    constructor(elem) {
        this.elem = elem;

        this.plugins = "image table textcolor paste link autolink fullscreen imagetools code customhr autosave lists codeeditor media";
        this.loadPlugins();

        this.tinyMceConfig = this.getTinyMceConfig();
        window.tinymce.init(this.tinyMceConfig);
    }

    loadPlugins() {
        codePlugin();
        customHrPlugin();
        if (document.querySelector('[drawio-enabled]').getAttribute('drawio-enabled') === 'true') {
            drawIoPlugin();
            this.plugins += ' drawio';
        }
    }

    getTinyMceConfig() {
        return {
            selector: '#html-editor',
            content_css: [
                window.baseUrl('/dist/styles.css'),
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
            plugins: this.plugins,
            imagetools_toolbar: 'imageoptions',
            toolbar: "undo redo | styleselect | bold italic underline strikethrough superscript subscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image-insert link hr drawio media | removeformat code fullscreen",
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
            media_alt_source: false,
            media_poster: false,
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
                    }, 'gallery');
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

                window.$events.listen('editor-scroll-to-text', textId => {
                    const element = editor.dom.get(textId)
                    if (!element) {
                        return;
                    }

                    // scroll the element into the view and put the cursor at the end.
                    element.scrollIntoView();
                    editor.selection.select(element, true);
                    editor.selection.collapse(false);
                    editor.focus();
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
                        }, 'gallery');
                    }
                });

                // Paste image-uploads
                editor.on('paste', event => editorPaste(event, editor));
            }
        };
    }

}

module.exports = WysiwygEditor;
