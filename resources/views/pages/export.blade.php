@extends('export-layout')

@section('title', $page->name)

@section('content')
    @include('pages.page-display')

    <hr>

    <div class="text-muted text-small">
        @include('partials.entity-export-meta', ['entity' => $page])
    </div>
@endsection