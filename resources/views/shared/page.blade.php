@extends('shared.layout')

@section('title', $page->name)

@section('sidebar')
    @if ($page->attachments->count() > 0)
        <div id="page-attachments" class="mb-l">
            <h5>{{ trans('entities.pages_attachments') }}</h5>
            <div class="body">
                @include('attachments.list', ['attachments' => $page->attachments, 'shareToken' => $token])
            </div>
        </div>
    @endif

    @include('shared.book-tree', [
        'book' => $book,
        'sidebarTree' => $sidebarTree,
        'current' => $page,
        'shareToken' => $token,
        'shareLink' => $shareLink ?? null
    ])
@endsection

@section('body')
    <main class="content-wrap card">
        <h1 class="break-text" id="bkmrk-page-title">{{ $page->name }}</h1>
        <div class="page-display-content" dir="auto">
            {!! $page->html !!}
        </div>
    </main>
@endsection

@section('right')
    <div id="page-details" class="entity-details mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links">
            @include('entities.meta', ['entity' => $page, 'watchOptions' => null])
        </div>
    </div>
@endsection
