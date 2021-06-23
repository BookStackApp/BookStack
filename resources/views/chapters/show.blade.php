@extends('tri-layout')

@section('container-attrs')
    component="entity-search"
    option:entity-search:entity-id="{{ $chapter->id }}"
    option:entity-search:entity-type="chapter"
@stop

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($chapter->description, 100) }}">
    <meta property="og:image" content="{{ $chapter->book->getBookCover() }}">
@endpush

@section('body')

    <div class="mb-m print-hidden">
        @include('partials.breadcrumbs', ['crumbs' => [
            $chapter->book,
            $chapter,
        ]])
    </div>

    <main class="content-wrap card">
        <h1 class="break-text">{{ $chapter->name }}</h1>
        <div refs="entity-search@contentView" class="chapter-content">
            <p class="text-muted break-text">{!! nl2br(e($chapter->description)) !!}</p>
            @if(count($pages) > 0)
                <div class="entity-list book-contents">
                    @foreach($pages as $page)
                        @include('pages.list-item', ['page' => $page])
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.chapters_empty') }}</p>

                    <div class="icon-list block inline">
                        @if(userCan('page-create', $chapter))
                            <a href="{{ $chapter->getUrl('/create-page') }}" class="icon-list-item text-page">
                                <span class="icon">@icon('page')</span>
                                <span>{{ trans('entities.books_empty_create_page') }}</span>
                            </a>
                        @endif
                        @if(userCan('book-update', $book))
                            <a href="{{ $book->getUrl('/sort') }}" class="icon-list-item text-book">
                                <span class="icon">@icon('book')</span>
                                <span>{{ trans('entities.books_empty_sort_current_book') }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endif
        </div>

        @include('partials.entity-search-results')
    </main>

    @include('partials.entity-sibling-navigation', ['next' => $next, 'previous' => $previous])

@stop

@section('right')

    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links text-small text-muted">
            @include('partials.entity-meta', ['entity' => $chapter])

            @if($book->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.books_permissions_active') }}
                    @endif
                </div>
            @endif

            @if($chapter->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $chapter))
                        <a href="{{ $chapter->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.chapters_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.chapters_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            @if(userCan('page-create', $chapter))
                <a href="{{ $chapter->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(userCan('chapter-update', $chapter))
                <a href="{{ $chapter->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCan('chapter-update', $chapter) && userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/move') }}" class="icon-list-item">
                    <span>@icon('folder')</span>
                    <span>{{ trans('common.move') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $chapter))
                <a href="{{ $chapter->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(signedInUser())
                @include('partials.entity-favourite-action', ['entity' => $chapter])
            @endif
            @include('partials.entity-export-menu', ['entity' => $chapter])
        </div>
    </div>
@stop

@section('left')

    @include('partials.entity-search-form', ['label' => trans('entities.chapters_search_this')])

    @if($chapter->tags->count() > 0)
        <div class="mb-xl">
            @include('components.tag-list', ['entity' => $chapter])
        </div>
    @endif

    @include('partials.book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop


