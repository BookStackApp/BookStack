@extends('layouts.tri')

@section('container-attrs')
    component="entity-search"
    option:entity-search:entity-id="{{ $book->id }}"
    option:entity-search:entity-type="book"
@stop

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($book->description, 100, '...') }}">
    @if($book->coverInfo()->exists())
        <meta property="og:image" content="{{ $book->coverInfo()->getUrl() }}">
    @endif
@endpush

@include('entities.body-tag-classes', ['entity' => $book])

@section('body')

    <div class="mb-s print-hidden">
        @include('entities.breadcrumbs', ['crumbs' => [
            $book,
        ]])
    </div>

    <main class="content-wrap card">
        <h1 class="break-text">{{$book->name}}</h1>
        <div refs="entity-search@contentView" class="book-content">
            <div class="text-muted break-text">{!! $book->descriptionInfo()->getHtml() !!}</div>
            @if(count($bookChildren) > 0)
                <div class="entity-list book-contents">
                    @foreach($bookChildren as $childElement)
                        @if($childElement->isA('chapter'))
                            @include('chapters.parts.list-item', ['chapter' => $childElement])
                        @else
                            @include('pages.parts.list-item', ['page' => $childElement])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.books_empty_contents') }}</p>

                    <div class="icon-list block inline">
                        @if(userCan(\BookStack\Permissions\Permission::PageCreate, $book))
                            <a href="{{ $book->getUrl('/create-page') }}" class="icon-list-item text-page">
                                <span class="icon">@icon('page')</span>
                                <span>{{ trans('entities.books_empty_create_page') }}</span>
                            </a>
                        @endif
                        @if(userCan(\BookStack\Permissions\Permission::ChapterCreate, $book))
                            <a href="{{ $book->getUrl('/create-chapter') }}" class="icon-list-item text-chapter">
                                <span class="icon">@icon('chapter')</span>
                                <span>{{ trans('entities.books_empty_add_chapter') }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endif
        </div>

        @include('entities.search-results')
    </main>

@stop

@section('right')
    @include('books.parts.show-sidebar-section-details', ['book' => $book, 'watchOptions' => $watchOptions])
    @include('books.parts.show-sidebar-section-actions', ['book' => $book, 'watchOptions' => $watchOptions])
@stop

@section('left')
    @include('entities.search-form', ['label' => trans('entities.books_search_this')])
    @include('books.parts.show-sidebar-section-tags', ['book' => $book])
    @include('books.parts.show-sidebar-section-shelves', ['bookParentShelves' => $bookParentShelves])
    @include('books.parts.show-sidebar-section-activity', ['activity' => $activity])
@stop

