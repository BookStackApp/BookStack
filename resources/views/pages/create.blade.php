@extends('base')

@section('head')
    <script src="/bower/tinymce-dist/tinymce.jquery.min.js"></script>
    <script src="/bower/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/js/image-manager.js"></script>
@stop

@section('content')
    <form action="{{$book->getUrl() . '/page'}}" method="POST">
        @include('pages/form')
    </form>

    <script>
        $(function() {
            $('#html').editable({inlineMode: false});
        });
    </script>
@stop