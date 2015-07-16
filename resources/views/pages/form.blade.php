
<div class="col-md-3 page-menu">
    <h4>You are editing a page</h4>
    <button type="submit" class="button pos">Save Page</button>
</div>

<div class="col-md-9 page-style editor">

    {{ csrf_field() }}
    <div class="title-input page-title">
            @include('form/text', ['name' => 'name', 'placeholder' => 'Enter Page Title'])
    </div>
    <div class="edit-area">
        @include('form/textarea', ['name' => 'html'])
    </div>

</div>





<script>
    $(function() {
        //ImageManager.show('#image-manager');

        tinymce.init({
            selector: '.edit-area textarea',
            content_css: '/css/app.css',
            body_class: 'container',
            relative_urls: false,
            height: 600,
            plugins: "image table textcolor paste link imagetools",
            toolbar: "undo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link | fontsizeselect full",
            content_style: "body {padding-left: 15px !important; padding-right: 15px !important; margin:0!important}",
            file_browser_callback: function(field_name, url, type, win) {
                ImageManager.show('#image-manager', function(image) {
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
            setup: function(editor) {
                editor.addButton('full', {
                    text: 'Expand',
                    icon: false,
                    onclick: function() {
                        var container = $(editor.getContainer()).toggleClass('fullscreen');
                    }
                });
            }
        });



    });
</script>