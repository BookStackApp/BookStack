@extends('layouts.export')

@section('title', $page->name)

@section('content')
    @include('pages.parts.page-display')

    <hr>

    <div class="text-muted text-small">
        @include('exports.parts.meta', ['entity' => $page])
    </div>
@endsection