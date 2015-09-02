


<div class="page-editor flex-fill flex">

    {{ csrf_field() }}
    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-4 faded">
                    <div class="action-buttons text-left">
                        <a onclick="$('body>header').slideToggle();" class="text-button text-primary"><i class="zmdi zmdi-swap-vertical"></i>Toggle Header</a>
                    </div>
                </div>
                <div class="col-md-8 faded">
                    <div class="action-buttons">
                        <a href="{{ back()->getTargetUrl() }}" class="text-button text-primary"><i class="zmdi zmdi-close"></i>Cancel</a>
                        <button type="submit" class="text-button  text-pos"><i class="zmdi zmdi-floppy"></i>Save Page</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="title-input page-title clearfix">
        <div class="input">
            @include('form/text', ['name' => 'name', 'placeholder' => 'Page Title'])
        </div>
    </div>
    <div class="edit-area flex-fill flex">
        <textarea id="html" name="html" rows="5"
                  @if($errors->has('html')) class="neg" @endif>@if(isset($model) || old('html')){{htmlspecialchars( old('html') ? old('html') : $model->html)}}@endif</textarea>
        @if($errors->has('html'))
            <div class="text-neg text-small">{{ $errors->first('html') }}</div>
        @endif
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
            //height: 700,
            extended_valid_elements: 'pre[*]',
            plugins: "image table textcolor paste link imagetools fullscreen code",
            toolbar: "code undo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link | fullscreen",
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