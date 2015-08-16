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
    @include('pages/image-manager')
    <script src="/js/image-manager.js"></script>
@stop