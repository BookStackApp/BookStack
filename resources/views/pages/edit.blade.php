@extends('base')

@section('head')
    <link rel="stylesheet" href="/plugins/css/froala_editor.min.css">
    <link rel="stylesheet" href="/plugins/css/froala_style.min.css">
    <script src="/plugins/js/froala_editor.min.js"></script>
@stop

@section('content')
    <form action="{{$page->getUrl()}}" method="POST">
        <input type="hidden" name="_method" value="PUT">
        @include('pages/form', ['model' => $page])
    </form>

    <script>
        $(function() {
            $('#html').editable({
                inlineMode: false,
                imageUploadURL: '/upload/image',
                imageUploadParams: {
                    '_token': '{{ csrf_token() }}'
                }
            });
        });
    </script>
@stop