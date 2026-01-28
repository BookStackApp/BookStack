@extends('shared.layout')

@section('title', $book->name)

@section('sidebar')
    @include('shared.book-tree', [
        'book' => $book,
        'sidebarTree' => $bookChildren,
        'current' => $book,
        'shareToken' => $token,
        'shareLink' => $shareLink ?? null
    ])
@endsection

@section('body')
    <main class="content-wrap card">
        <h1 class="break-text">{{ $book->name }}</h1>
        <div class="book-content">
            <div class="text-muted break-text">{!! $book->descriptionInfo()->getHtml() !!}</div>
            @if(count($bookChildren) > 0)
                <div class="entity-list book-contents">
                    @foreach($bookChildren as $childElement)
                        @if($childElement->isA('chapter'))
                            @include('shared.chapters.list-item', [
                                'chapter' => $childElement,
                                'shareLink' => $shareLink,
                                'token' => $token
                            ])
                        @else
                            @include('shared.pages.list-item', [
                                'page' => $childElement,
                                'shareLink' => $shareLink,
                                'token' => $token
                            ])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.books_empty_contents') }}</p>
                </div>
            @endif
        </div>
    </main>
@endsection

@section('right')
    <div id="book-details" class="entity-details mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links">
            @include('entities.meta', ['entity' => $book, 'watchOptions' => null])
        </div>
    </div>
@endsection
