@extends('base')

@section('head')
    <script src="/libs/tinymce/tinymce.min.js?ver=4.3.2"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex">
        <form action="{{$page->getUrl()}}" method="POST" class="flex flex-fill">
            <input type="hidden" name="_method" value="PUT">
            @include('pages/form', ['model' => $page])
        </form>
    </div>
    <image-manager image-type="gallery"></image-manager>

@stop