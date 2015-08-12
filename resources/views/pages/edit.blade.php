@extends('base')

@section('head')
    <script src="/bower/tinymce-dist/tinymce.jquery.min.js"></script>
@stop

@section('content')

    <form action="{{$page->getUrl()}}" method="POST">
        <input type="hidden" name="_method" value="PUT">
        @include('pages/form', ['model' => $page])
    </form>

@stop

@section('bottom')
    <div id="image-manager-container"></div>
    <script src="/js/image-manager.js"></script>
@stop