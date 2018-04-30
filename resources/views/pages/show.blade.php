@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-8 col-xs-5 faded">
        @include('pages._breadcrumbs', ['page' => $page])
    </div>
    <div class="col-sm-4 col-xs-7 faded">
        <div class="action-buttons">
            <span dropdown class="dropdown-container">
                <div dropdown-toggle class="text-button text-primary">@icon('export'){{ trans('entities.export') }}</div>
                <ul class="wide">
                    <li><a href="{{ $page->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $page->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $page->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </span>
            @if(userCan('page-update', $page))
                <a href="{{ $page->getUrl('/edit') }}" class="text-primary text-button" >@icon('edit'){{ trans('common.edit') }}</a>
            @endif
            @if(userCan('page-update', $page) || userCan('restrictions-manage', $page) || userCan('page-delete', $page))
                <div dropdown class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button">@icon('more') {{ trans('common.more') }}</a>
                    <ul>
                        @if(userCan('page-update', $page))
                            <li><a href="{{ $page->getUrl('/copy') }}" class="text-primary" >@icon('copy'){{ trans('common.copy') }}</a></li>
                            <li><a href="{{ $page->getUrl('/move') }}" class="text-primary" >@icon('folder'){{ trans('common.move') }}</a></li>
                            <li><a href="{{ $page->getUrl('/revisions') }}" class="text-primary">@icon('history'){{ trans('entities.revisions') }}</a></li>
                        @endif
                        @if(userCan('restrictions-manage', $page))
                            <li><a href="{{ $page->getUrl('/permissions') }}" class="text-primary">@icon('lock'){{ trans('entities.permissions') }}</a></li>
                        @endif
                        @if(userCan('page-delete', $page))
                            <li><a href="{{ $page->getUrl('/delete') }}" class="text-neg">@icon('delete'){{ trans('common.delete') }}</a></li>
                        @endif
                    </ul>
                </div>
            @endif

        </div>
    </div>
@stop

@section('sidebar')

    @if($page->tags->count() > 0)
        <div class="card tag-display">
            <h3>@icon('tag') {{ trans('entities.page_tags') }}</h3>
            <div class="body">
                @include('components.tag-list', ['entity' => $page])
            </div>
        </div>
    @endif

    @if ($page->attachments->count() > 0)
        <div class="card">
            <h3>@icon('attach') {{ trans('entities.pages_attachments') }}</h3>
            <div class="body">
                @foreach($page->attachments as $attachment)
                    <div class="attachment">
                        <a href="{{ $attachment->getUrl() }}" @if($attachment->external) target="_blank" @endif>@icon($attachment->external ? 'export' : 'file'){{ $attachment->name }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (isset($pageNav) && count($pageNav))
        <div class="card">
            <h3>@icon('open-book') {{ trans('entities.pages_navigation') }}</h3>
            <div class="body">
                <div class="sidebar-page-nav menu">
                    @foreach($pageNav as $navItem)
                        <li class="page-nav-item h{{ $navItem['level'] }}">
                            <a href="{{ $navItem['link'] }}">{{ $navItem['text'] }}</a>
                        </li>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="card entity-details">
        <h3>@icon('info') {{ trans('common.details') }}</h3>
        <div class="body text-muted text-small blended-links">
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
        </div>
    </div>

    @include('partials/book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])

@stop

@section('body')
    <div class="page-content" page-display="{{ $page->id }}" ng-non-bindable>

        <div class="pointer-container" id="pointer">
            <div class="pointer anim" >
                <span class="icon text-primary">@icon('link') @icon('include', ['style' => 'display:none;'])</span>
                <input readonly="readonly" type="text" id="pointer-url" placeholder="url">
                <button class="button icon" data-clipboard-target="#pointer-url" type="button" title="{{ trans('entities.pages_copy_link') }}">@icon('copy')</button>
            </div>
        </div>

        @include('pages/page-display')

    </div>
    @if ($commentsEnabled)
      <div class="container small nopad">
          @include('comments/comments', ['page' => $page])
      </div>
    @endif
@stop
