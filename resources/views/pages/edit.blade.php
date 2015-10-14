@extends('base')

@section('head')
    <script src="/bower/tinymce-dist/tinymce.jquery.min.js"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex">
        <form action="{{$page->getUrl()}}" method="POST" class="flex flex-fill">
            <input type="hidden" name="_method" value="PUT">
            @include('pages/form', ['model' => $page])
        </form>
    </div>
    <image-manager></image-manager>

@stop