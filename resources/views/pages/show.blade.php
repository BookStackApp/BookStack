@extends('tri-layout')

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($page->text, 100, '...') }}">
    <meta property="og:image" content="{{ $page->getCoverImage() }}">
@endpush

@section('body')

    <div class="mb-m print-hidden">
        @include('partials.breadcrumbs', ['crumbs' => [
            $page->book,
            $page->hasChapter() ? $page->chapter : null,
            $page,
        ]])
    </div>

    <main class="content-wrap card">
        <div class="page-content clearfix" page-display="{{ $page->id }}">
            @include('pages.pointer', ['page' => $page])
            @include('pages.page-display')
        </div>
    </main>

    @include('partials.entity-sibling-navigation', ['next' => $next, 'previous' => $previous])

    @if ($commentsEnabled)
        @if(($previous || $next))
            <div class="px-xl">
                <hr class="darker">
            </div>
        @endif

        <div class="px-xl comments-container mb-l print-hidden">
            @include('comments.comments', ['page' => $page])
            <div class="clearfix"></div>
        </div>
    @endif
@stop

@section('left')

    @if($page->tags->count() > 0)
        <section>
            @include('components.tag-list', ['entity' => $page])
        </section>
    @endif

    @if ($page->attachments->count() > 0)
        <div id="page-attachments" class="mb-l">
            <h5>{{ trans('entities.pages_attachments') }}</h5>
            <div class="body">
                @include('attachments.list', ['attachments' => $page->attachments])
            </div>
        </div>
    @endif

    @if (isset($pageNav) && count($pageNav))
        <nav id="page-navigation" class="mb-xl" aria-label="{{ trans('entities.pages_navigation') }}">
            <h5>{{ trans('entities.pages_navigation') }}</h5>
            <div class="body">
                <div class="sidebar-page-nav menu">
                    @foreach($pageNav as $navItem)
                        <li class="page-nav-item h{{ $navItem['level'] }}">
                            <a href="{{ $navItem['link'] }}" class="text-limit-lines-1 block">{{ $navItem['text'] }}</a>
                            <div class="primary-background sidebar-page-nav-bullet"></div>
                        </li>
                    @endforeach
                </div>
            </div>
        </nav>
    @endif

    @include('partials.book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop

@section('right')
    <div id="page-details" class="entity-details mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="body text-small blended-links">
            @include('partials.entity-meta', ['entity' => $page])

            @if($book->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.books_permissions_active') }}
                    @endif
                </div>
            @endif

            @if($page->chapter && $page->chapter->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $page->chapter))
                        <a href="{{ $page->chapter->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.chapters_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.chapters_permissions_active') }}
                    @endif
                </div>
            @endif

            @if($page->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $page))
                        <a href="{{ $page->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.pages_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.pages_permissions_active') }}
                    @endif
                </div>
            @endif

            @if($page->template)
                <div>
                    @icon('template'){{ trans('entities.pages_is_template') }}
                </div>
            @endif
        </div>
    </div>

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>

        <div class="icon-list text-primary">

            {{--User Actions--}}
            @if(userCan('page-update', $page))
                <a href="{{ $page->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCanOnAny('page-create'))
                <a href="{{ $page->getUrl('/copy') }}" class="icon-list-item">
                    <span>@icon('copy')</span>
                    <span>{{ trans('common.copy') }}</span>
                </a>
            @endif
            @if(userCan('page-update', $page))
                @if(userCan('page-delete', $page))
	                <a href="{{ $page->getUrl('/move') }}" class="icon-list-item">
	                    <span>@icon('folder')</span>
	                    <span>{{ trans('common.move') }}</span>
	                </a>
                @endif
                <a href="{{ $page->getUrl('/revisions') }}" class="icon-list-item">
                    <span>@icon('history')</span>
                    <span>{{ trans('entities.revisions') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $page))
                <a href="{{ $page->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('page-delete', $page))
                <a href="{{ $page->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(signedInUser())
                @include('partials.entity-favourite-action', ['entity' => $page])
            @endif
            @include('partials.entity-export-menu', ['entity' => $page])
        </div>

    </div>
@stop
