@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-6 faded">
        <div class="action-buttons text-left">
            <a expand-toggle=".entity-list.compact .entity-item-snippet" class="text-primary text-button">@icon('expand-text'){{ trans('common.toggle_details') }}</a>
        </div>
    </div>
@stop

@section('body')

    <div class="container" ng-non-bindable>
        <div class="row">

            <div class="col-sm-4">
                @if(count($draftPages) > 0)
                    <div id="recent-drafts" class="card">
                        <h3>@icon('edit') {{ trans('entities.my_recent_drafts') }}</h3>
                        @include('partials/entity-list', ['entities' => $draftPages, 'style' => 'compact'])
                    </div>
                @endif

                <div class="card">
                    <h3>@icon($signedIn ? 'view' : 'star-circle') {{ trans('entities.' . ($signedIn ? 'my_recently_viewed' : 'books_recent')) }}</h3>
                    @include('partials/entity-list', [
                        'entities' => $recents,
                        'style' => 'compact',
                        'emptyText' => $signedIn ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
                        ])
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <h3>@icon('file') <a class="no-color" href="{{ baseUrl("/pages/recently-updated") }}">{{ trans('entities.recently_updated_pages') }}</a></h3>
                    <div id="recently-updated-pages">
                        @include('partials/entity-list', [
                        'entities' => $recentlyUpdatedPages,
                        'style' => 'compact',
                        'emptyText' => trans('entities.no_pages_recently_updated')
                        ])
                    </div>
                </div>
            </div>

            <div class="col-sm-4" id="recent-activity">
                <div class="card">
                    <h3>@icon('time') {{ trans('entities.recent_activity') }}</h3>
                    @include('partials/activity-list', ['activity' => $activity])
                </div>
            </div>

        </div>
    </div>


@stop
