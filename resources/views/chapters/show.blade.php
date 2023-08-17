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
            <p class="text-muted break-text">{!! nl2br(e($chapter->description)) !!}</p>
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

        @include('entities.search-results')
    </main>

    @include('entities.sibling-navigation', ['next' => $next, 'previous' => $previous])

@stop

@section('right')

    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links">
            @include('entities.meta', ['entity' => $chapter, 'watchOptions' => $watchOptions])

            @if($book->hasPermissions())
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}" class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.books_permissions_active') }}</div>
                        </a>
                    @else
                        <div class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.books_permissions_active') }}</div>
                        </div>
                    @endif
                </div>
            @endif

            @if($chapter->hasPermissions())
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $chapter))
                        <a href="{{ $chapter->getUrl('/permissions') }}" class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.chapters_permissions_active') }}</div>
                        </a>
                    @else
                        <div class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.chapters_permissions_active') }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-link">

            @if(userCan('page-create', $chapter))
                <a href="{{ $chapter->getUrl('/create-page') }}" data-shortcut="new" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(userCan('chapter-update', $chapter))
                <a href="{{ $chapter->getUrl('/edit') }}" data-shortcut="edit" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCanOnAny('create', \BookStack\Entities\Models\Book::class) || userCan('chapter-create-all') || userCan('chapter-create-own'))
                <a href="{{ $chapter->getUrl('/copy') }}" data-shortcut="copy" class="icon-list-item">
                    <span>@icon('copy')</span>
                    <span>{{ trans('common.copy') }}</span>
                </a>
            @endif
            @if(userCan('chapter-update', $chapter) && userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/move') }}" data-shortcut="move" class="icon-list-item">
                    <span>@icon('folder')</span>
                    <span>{{ trans('common.move') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $chapter))
                <a href="{{ $chapter->getUrl('/permissions') }}" data-shortcut="permissions" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/delete') }}" data-shortcut="delete" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            @if($chapter->book && userCan('book-update', $chapter->book))
                <hr class="primary-background"/>
                <a href="{{ $chapter->book->getUrl('/sort') }}" data-shortcut="sort" class="icon-list-item">
                    <span>@icon('sort')</span>
                    <span>{{ trans('entities.chapter_sort_book') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if($watchOptions->canWatch() && !$watchOptions->isWatching())
                @include('entities.watch-action', ['entity' => $chapter])
            @endif
            @if(signedInUser())
                @include('entities.favourite-action', ['entity' => $chapter])
            @endif
            @if(userCan('content-export'))
                @include('entities.export-menu', ['entity' => $chapter])
            @endif
        </div>
    </div>
@stop

@section('left')

    @include('entities.search-form', ['label' => trans('entities.chapters_search_this')])

    @if($chapter->tags->count() > 0)
        <div class="mb-xl">
            @include('entities.tag-list', ['entity' => $chapter])
        </div>
    @endif

    @include('entities.book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop


