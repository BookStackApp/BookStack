@extends('base')

@section('head')
    <script src="/bower/tinymce-dist/tinymce.jquery.min.js"></script>
    <script src="/bower/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/js/image-manager.js"></script>
@stop

@section('content')
    <form action="{{$page->getUrl()}}" method="POST">
        <input type="hidden" name="_method" value="PUT">
        @include('pages/form', ['model' => $page])
    </form>

    <section class="overlay" style="display:none;">
        <div id="image-manager">
            <div class="image-manager-left">
                <div class="image-manager-header">
                    <button type="button" class="button neg float right" data-action="close">Close</button>
                    <div class="image-manager-title">Image Library</div>
                </div>
                <div class="image-manager-display">
                </div>
                <form action="/upload/image" class="image-manager-dropzone">
                    {{ csrf_field() }}
                    Drag images or click here to upload
                </form>
            </div>
            {{--<div class="sidebar">--}}

            {{--</div>--}}
        </div>
    </section>

    <script>
        $(function() {
            //ImageManager.show('#image-manager');

            tinymce.init({
                selector: '.edit-area textarea',
                content_css: '/css/app.css',
                body_class: 'container',
                plugins: "autoresize image table textcolor paste link imagetools",
                content_style: "body {padding-left: 15px !important; padding-right: 15px !important;}",
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
                }
            });



        });
    </script>
@stop