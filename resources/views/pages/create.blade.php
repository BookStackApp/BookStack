@extends('base')

@section('head')
    <link rel="stylesheet" href="/plugins/css/froala_editor.min.css">
    <link rel="stylesheet" href="/plugins/css/froala_style.min.css">
    <script src="/plugins/js/froala_editor.min.js"></script>
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