


<div class="page-style editor">

    {{ csrf_field() }}
    <div class="title-input page-title clearfix">
        <div class="input">
            @include('form/text', ['name' => 'name', 'placeholder' => 'Enter Page Title'])
        </div>
    </div>
    <div class="edit-area">
        @include('form/textarea', ['name' => 'html'])
    </div>
    <div class="margin-top large">
        <a onclick="window.history.back();" class="button muted">Cancel</a>
        <button type="submit" class="button pos">Save Page</button>
    </div>
</div>





<script>
    $(function() {
        //ImageManager.show('#image-manager');

        tinymce.init({
            selector: '.edit-area textarea',
            content_css: [
                '/css/app.css',
                '//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300'
            ],
            body_class: 'page-content',
            relative_urls: false,
            statusbar: false,
            menubar: false,
            height: 700,
            plugins: "image table textcolor paste link imagetools fullscreen",
            toolbar: "undo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link | fontsizeselect fullscreen",
            content_style: "body {padding-left: 15px !important; padding-right: 15px !important; margin:0!important; margin-left:auto!important;margin-right:auto!important;}",
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
//            setup: function(editor) {
//                editor.addButton('full', {
//                    title: 'Expand Editor',
//                    icon: 'fullscreen',
//                    onclick: function() {
//                        var container = $(editor.getContainer()).toggleClass('fullscreen');
//                        var isFull = container.hasClass('fullscreen');
//                        var iframe = container.find('iframe').first();
//                        var height = isFull ? $(window).height()-110 : 600;
//                        iframe.css('height', height + 'px');
//                    }
//                });
//            }
        });



    });
</script>