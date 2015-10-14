
module.exports = {
    selector: '#html-editor',
    content_css: [
        '/css/styles.css',
        '//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300'
    ],
    body_class: 'page-content',
    relative_urls: false,
    statusbar: false,
    menubar: false,
    paste_data_images: false,
    //height: 700,
    extended_valid_elements: 'pre[*]',
    automatic_uploads: false,
    valid_children: "-div[p|pre|h1|h2|h3|h4|h5|h6|blockquote]",
    plugins: "image table textcolor paste link imagetools fullscreen code hr",
    toolbar: "undo redo | styleselect | bold italic underline strikethrough superscript subscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link hr | code fullscreen",
    content_style: "body {padding-left: 15px !important; padding-right: 15px !important; margin:0!important; margin-left:auto!important;margin-right:auto!important;}",
    style_formats: [
        {title: "Header 1", format: "h1"},
        {title: "Header 2", format: "h2"},
        {title: "Header 3", format: "h3"},
        {title: "Header 4", format: "h4"},
        {title: "Paragraph", format: "p"},
        {title: "Blockquote", format: "blockquote"},
        {title: "Code Block", icon: "code", format: "pre"},
        {title: "Inline Code", icon: "code", inline: "code"}
    ],
    file_browser_callback: function(field_name, url, type, win) {
        ImageManager.show(function(image) {
            win.document.getElementById(field_name).value = image.url;
            if ("createEvent" in document) {
                var evt = document.createEvent("HTMLEvents");
                evt.initEvent("change", false, true);
                win.document.getElementById(field_name).dispatchEvent(evt);
            } else {
                win.document.getElementById(field_name).fireEvent("onchange");
            }
        });
    },
    paste_preprocess: function(plugin, args) {
        var content = args.content;
        if(content.indexOf('<img src="file://') !== -1) {
            args.content = '';
        }
    },
    setup: function(editor) {
        // Paste image-uploads
        editor.on('paste', function(e) {
            if(e.clipboardData) {
                var items = e.clipboardData.items;
                if (items){
                    for (var i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf("image") !== -1) {

                            var file = items[i].getAsFile();
                            var formData = new FormData();
                            var ext = 'png';
                            var xhr = new XMLHttpRequest();

                            if (file.name) {
                                var fileNameMatches = file.name.match(/\.(.+)$/);
                                if (fileNameMatches) {
                                    ext = fileNameMatches[1];
                                }
                            }

                            var id = "image-" + Math.random().toString(16).slice(2);
                            editor.execCommand('mceInsertContent', false, '<img src="/loading.gif" id="'+id+'">');

                            var remoteFilename = "image-" + Date.now() + "." + ext;
                            formData.append('file', file, remoteFilename);
                            formData.append('_token', document.querySelector('meta[name="token"]').getAttribute('content'));

                            xhr.open('POST', '/upload/image');
                            xhr.onload = function() {
                                if (xhr.status === 200 || xhr.status === 201) {
                                    var result = JSON.parse(xhr.responseText);
                                    //var newImage =  editor.getDoc().getElementById(id);
                                    //newImage.setAttribute('src', result.url);
                                    editor.dom.setAttrib(id, 'src', result.url);
                                    console.log(result);
                                } else {
                                    console.log('An error occured uploading the image');
                                    console.log(xhr.responseText);
                                }
                            };
                            xhr.send(formData);
                        }
                    }
                }

            }
        });
    }
};