@extends('layouts.tri')

@section('container-attrs')
    component="entity-search"
    option:entity-search:entity-id="{{ $chapter->id }}"
    option:entity-search:entity-type="chapter"
@stop

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($chapter->description, 100, '...') }}">
@endpush

@include('entities.body-tag-classes', ['entity' => $chapter])

@section('body')

    <div class="mb-m print-hidden">
        @include('entities.breadcrumbs', ['crumbs' => [
            $chapter->book,
            $chapter,
        ]])
    </div>

    <main class="content-wrap card">
        <h1 class="break-text">{{ $chapter->name }}</h1>
        <div refs="entity-search@contentView" class="chapter-content">
            <div class="text-muted break-text">{!! $chapter->descriptionInfo()->getHtml() !!}</div>
            @if(count($pages) > 0)
                <div class="entity-list book-contents">
                    @foreach($pages as $page)
                        @include('pages.parts.list-item', ['page' => $page])
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.chapters_empty') }}</p>

                    <div class="icon-list block inline">
                        @if(userCan(\BookStack\Permissions\Permission::PageCreate, $chapter))
                            <a href="{{ $chapter->getUrl('/create-page') }}" class="icon-list-item text-page">
                                <span class="icon">@icon('page')</span>
                                <span>{{ trans('entities.books_empty_create_page') }}</span>
                            </a>
                        @endif
                        @if(userCan(\BookStack\Permissions\Permission::BookUpdate, $book))
                            <a href="{{ $book->getUrl('/sort') }}" class="icon-list-item text-book">
                                <span class="icon">@icon('book')</span>
                                <span>{{ trans('entities.books_empty_sort_current_book') }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endif
        </div>

        @include('entities.search-results')
    </main>

    @include('entities.sibling-navigation', ['next' => $next, 'previous' => $previous])

@stop

@section('right')
    @include('chapters.parts.show-sidebar-section-details', ['chapter' => $chapter, 'book' => $book, 'watchOptions' => $watchOptions])
    @include('chapters.parts.show-sidebar-section-actions', ['chapter' => $chapter, 'watchOptions' => $watchOptions])
@stop

@section('left')
    @include('entities.search-form', ['label' => trans('entities.chapters_search_this')])
    @include('chapters.parts.show-sidebar-section-tags', ['chapter' => $chapter])
    @include('entities.book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop


