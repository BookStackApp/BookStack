@extends('shared.layout')

@section('title', $chapter->name)

@section('sidebar')
    @include('shared.book-tree', [
        'book' => $book,
        'sidebarTree' => $sidebarTree,
        'current' => $chapter,
        'shareToken' => $token,
        'shareLink' => $shareLink ?? null
    ])
@endsection

@section('body')
    <main class="content-wrap card">
        <h1 class="break-text">{{ $chapter->name }}</h1>
        <div class="chapter-content">
            <div class="text-muted break-text">{!! $chapter->descriptionInfo()->getHtml() !!}</div>
            @if(count($pages) > 0)
                <div class="entity-list book-contents">
                    @foreach($pages as $page)
                        @include('shared.pages.list-item', [
                            'page' => $page,
                            'shareLink' => $shareLink ?? null,
                            'token' => $token
                        ])
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.chapters_empty') }}</p>
                </div>
            @endif
        </div>
    </main>
@endsection

@section('right')
    <div id="chapter-details" class="entity-details mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links">
            @include('entities.meta', ['entity' => $chapter, 'watchOptions' => null])
        </div>
    </div>
@endsection
