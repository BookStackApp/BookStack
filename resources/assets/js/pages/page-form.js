"use strict";

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
    editor.addShortcut('meta+e', '', ['FormatBlock', false, 'pre']);
    editor.addShortcut('meta+shift+E', '', ['FormatBlock', false, 'code']);
}

var mceOptions = module.exports = {
    selector: '#html-editor',
    content_css: [
        window.baseUrl('/css/styles.css'),
        window.baseUrl('/libs/material-design-iconic-font/css/material-design-iconic-font.min.css')
    ],
    body_class: 'page-content',
    relative_urls: false,
    remove_script_host: false,
    document_base_url: window.baseUrl('/'),
    statusbar: false,
    menubar: false,
    paste_data_images: false,
    extended_valid_elements: 'pre[*]',
    automatic_uploads: false,
    valid_children: "-div[p|pre|h1|h2|h3|h4|h5|h6|blockquote]",
    plugins: "image table textcolor paste link fullscreen imagetools code customhr autosave lists",
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
        {title: "Code Block", icon: "code", format: "pre"},
        {title: "Inline Code", icon: "code", inline: "code"},
        {title: "Callouts", items: [
            {title: "Success", block: 'p', exact: true, attributes : {'class' : 'callout success'}},
            {title: "Info", block: 'p', exact: true, attributes : {'class' : 'callout info'}},
            {title: "Warning", block: 'p', exact: true, attributes : {'class' : 'callout warning'}},
            {title: "Danger", block: 'p', exact: true, attributes : {'class' : 'callout danger'}}
        ]}
    ],
    style_formats_merge: false,
    formats: {
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
        for (let i = 0; i < mceOptions.extraSetups.length; i++) {
            mceOptions.extraSetups[i](editor);
        }

        registerEditorShortcuts(editor);

        (function () {
            var wrap;

            function hasTextContent(node) {
                return node && !!( node.textContent || node.innerText );
            }

            editor.on('dragstart', function () {
                var node = editor.selection.getNode();

                if (node.nodeName !== 'IMG') return;
                wrap = editor.dom.getParent(node, '.mceTemp');

                if (!wrap && node.parentNode.nodeName === 'A' && !hasTextContent(node.parentNode)) {
                    wrap = node.parentNode;
                }
            });

            editor.on('drop', function (event) {
                var dom = editor.dom,
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
        })();

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