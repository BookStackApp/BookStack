
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
    //height: 700,
    extended_valid_elements: 'pre[*]',
    valid_children: "-div[p|pre|h1|h2|h3|h4|h5|h6|blockquote]",
    plugins: "image table textcolor paste link imagetools fullscreen code hr",
    toolbar: "code undo | styleselect | hr bold italic underline strikethrough superscript subscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link | fullscreen",
    content_style: "body {padding-left: 15px !important; padding-right: 15px !important; margin:0!important; margin-left:auto!important;margin-right:auto!important;}",
    style_formats: [
        {title: "Header 1", format: "h1"},
        {title: "Header 2", format: "h2"},
        {title: "Header 3", format: "h3"},
        {title: "Header 4", format: "h4"},
        {title: "Paragraph", format: "p"},
        {title: "Blockquote", format: "blockquote"},
        {title: "Code Block", icon: "code", format: "pre"}
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
    }
};