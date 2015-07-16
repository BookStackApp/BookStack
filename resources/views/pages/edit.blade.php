@extends('base')

@section('head')
    <script src="/bower/tinymce-dist/tinymce.jquery.min.js"></script>
    <script src="/bower/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/js/image-manager.js"></script>
@stop

@section('content')

    <div class="row">
        <form action="{{$page->getUrl()}}" method="POST">
            <input type="hidden" name="_method" value="PUT">
            @include('pages/form', ['model' => $page])
        </form>
    </div>

@stop

@section('bottom')
    @include('pages/image-manager')
@stop