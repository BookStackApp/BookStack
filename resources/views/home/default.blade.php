@extends('layouts.simple')

@section('body')

    <div class="container px-xl py-s">
        <div class="grid half">
            <div>
                <div class="icon-list inline block">
                    @include('home.parts.expand-toggle', ['classes' => 'text-muted text-link', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
                </div>
            </div>
            <div class="text-m-right">
                <div class="icon-list inline block">
                    @include('common.dark-mode-toggle', ['classes' => 'text-muted icon-list-item text-link'])
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="home-default">
        <div class="grid third gap-xxl no-row-gap" >
            <div>
                @if(count($draftPages) > 0)
                    <div id="recent-drafts" class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.my_recent_drafts') }}</h3>
                        <div class="px-m">
                            @include('entities.list', ['entities' => $draftPages, 'style' => 'compact'])
                        </div>
                    </div>
                @endif

                <div id="{{ auth()->check() ? 'recently-viewed' : 'recent-books' }}" class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')) }}</h3>
                    <div class="px-m">
                        @include('entities.list', [
                        'entities' => $recents,
                        'style' => 'compact',
                        'emptyText' => auth()->check() ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
                        ])
                    </div>
                </div>
            </div>

            <div>
                @if(count($favourites) > 0)
                    <div id="top-favourites" class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.my_most_viewed_favourites') }}</h3>
                        <div class="px-m">
                            @include('entities.list', [
                            'entities' => $favourites,
                            'style' => 'compact',
                            ])
                        </div>
                        <a href="{{ url('/favourites')  }}" class="card-footer-link">{{ trans('common.view_all') }}</a>
                    </div>
                @endif

                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.recently_updated_pages') }}</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                        'entities' => $recentlyUpdatedPages,
                        'style' => 'compact',
                        'emptyText' => trans('entities.no_pages_recently_updated'),
                        ])
                    </div>
                    <a href="{{ url("/pages/recently-updated") }}" class="card-footer-link">{{ trans('common.view_all') }}</a>
                </div>
            </div>

            <div>
                <div id="recent-activity">
                    <div class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.recent_activity') }}</h3>
                        @include('common.activity-list', ['activity' => $activity])
                    </div>
                </div>
            </div>

        </div>
    </div>

@stop
