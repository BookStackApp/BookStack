@extends('simple-layout')

@section('body')

    <div class="container px-xl py-s">
        <div class="grid half">
            <div>
                <div class="icon-list inline block">
                    @include('components.expand-toggle', ['target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
                </div>
            </div>
            <div class="text-m-right">
                <div class="icon-list inline block">
                    @include('partials.dark-mode-toggle', ['classes' => 'text-muted icon-list-item text-primary'])
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
                            @include('partials.entity-list', ['entities' => $draftPages, 'style' => 'compact'])
                        </div>
                    </div>
                @endif

                <div id="{{ auth()->check() ? 'recently-viewed' : 'recent-books' }}" class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')) }}</h3>
                    <div class="px-m">
                        @include('partials.entity-list', [
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
                        <h3 class="card-title">
                            <a href="{{ url('/favourites') }}" class="no-color">{{ trans('entities.my_most_viewed_favourites') }}</a>
                        </h3>
                        <div class="px-m">
                            @include('partials.entity-list', [
                            'entities' => $favourites,
                            'style' => 'compact',
                            ])
                        </div>
                    </div>
                @endif

                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title"><a class="no-color" href="{{ url("/pages/recently-updated") }}">{{ trans('entities.recently_updated_pages') }}</a></h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('partials.entity-list', [
                        'entities' => $recentlyUpdatedPages,
                        'style' => 'compact',
                        'emptyText' => trans('entities.no_pages_recently_updated')
                        ])
                    </div>
                </div>
            </div>

            <div>
                <div id="recent-activity">
                    <div class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.recent_activity') }}</h3>
                        @include('partials.activity-list', ['activity' => $activity])
                    </div>
                </div>
            </div>

        </div>
    </div>

@stop
