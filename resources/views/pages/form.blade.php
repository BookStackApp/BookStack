


<div class="page-style editor">

    {{ csrf_field() }}
    <div class="title-input title-strip clearfix">
        <button type="submit" class="button pos float right">Save Page</button>
        <div class="float left">
            @include('form/text', ['name' => 'name', 'placeholder' => 'Enter Page Title'])
        </div>
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
            statusbar: false,
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
                    title: 'Expand Editor',
                    icon: 'fullscreen',
                    onclick: function() {
                        var container = $(editor.getContainer()).toggleClass('fullscreen');
                        var isFull = container.hasClass('fullscreen');
                        var iframe = container.find('iframe').first();
                        var height = isFull ? $(window).height()-110 : 600;
                        iframe.css('height', height + 'px');
                    }
                });
            }
        });



    });
</script>