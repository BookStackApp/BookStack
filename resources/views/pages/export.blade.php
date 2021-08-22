@extends('layouts.export')

@section('title', $page->name)

@section('content')
    @include('pages.parts.page-display')

    <hr>

    <div class="text-muted text-small">
        @include('entities.export-meta', ['entity' => $page])
    </div>
@endsection