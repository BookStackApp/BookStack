@extends('layouts.base')

@push('body-class', 'flexbox ')

@section('content')

    <div id="main-content" class="flex-fill flex fill-height">
        <form action="{{ $page->getUrl() }}" autocomplete="off" data-page-id="{{ $page->id }}" method="POST" class="flex flex-fill">
            {{ csrf_field() }}

            @if(!$isDraft) {{ method_field('PUT') }} @endif
            @include('pages.parts.form', ['model' => $page])
        </form>
    </div>
    
    @include('pages.parts.image-manager', ['uploaded_to' => $page->id])
    @include('pages.parts.code-editor')
    @include('entities.selector-popup')
@stop