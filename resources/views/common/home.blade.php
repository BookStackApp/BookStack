@extends('simple-layout')


@section('body')

    <div class="container px-xl py-l">
        <a expand-toggle=".entity-list.compact .entity-item-snippet" class="text-muted">@icon('expand-text'){{ trans('common.toggle_details') }}</a>
    </div>

    <div class="container" id="home-default">
        <div class="grid third gap-xxl" >
            <div>
                @if(count($draftPages) > 0)
                    <div id="recent-drafts" class="card mb-xl">
                        <h3>{{ trans('entities.my_recent_drafts') }}</h3>
                        <div class="px-m">
                            @include('partials.entity-list', ['entities' => $draftPages, 'style' => 'compact'])
                        </div>
                    </div>
                @endif

                <div id="{{ $signedIn ? 'recently-viewed' : 'recent-books' }}" class="card mb-xl">
                    <h3>{{ trans('entities.' . ($signedIn ? 'my_recently_viewed' : 'books_recent')) }}</h3>
                    <div class="px-m">
                        @include('partials.entity-list', [
                        'entities' => $recents,
                        'style' => 'compact',
                        'emptyText' => $signedIn ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
                        ])
                    </div>
                </div>
            </div>

            <div>
                <div id="recent-pages" class="card mb-xl">
                    <h3><a class="no-color" href="{{ baseUrl("/pages/recently-updated") }}">{{ trans('entities.recently_updated_pages') }}</a></h3>
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
                        <h3>{{ trans('entities.recent_activity') }}</h3>
                        @include('partials.activity-list', ['activity' => $activity])
                    </div>
                </div>
            </div>

        </div>
    </div>



@stop
