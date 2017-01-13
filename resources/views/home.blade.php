@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 faded">
                    <div class="action-buttons text-left">
                        <a data-action="expand-entity-list-details" class="text-primary text-button"><i class="zmdi zmdi-wrap-text"></i>{{ trans('common.toggle_details') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" ng-non-bindable>
        <div class="row">

            <div class="col-sm-4">
                <div id="recent-drafts">
                    @if(count($draftPages) > 0)
                        <h4>{{ trans('entities.my_recent_drafts') }}</h4>
                        @include('partials/entity-list', ['entities' => $draftPages, 'style' => 'compact'])
                    @endif
                </div>
                @if($signedIn)
                    <h4>{{ trans('entities.my_recently_viewed') }}</h4>
                @else
                    <h4>{{ trans('entities.books_recent') }}</h4>
                @endif
                @include('partials/entity-list', [
                'entities' => $recents,
                'style' => 'compact',
                'emptyText' => $signedIn ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
                ])
            </div>

            <div class="col-sm-4">
                <h4><a class="no-color" href="{{ baseUrl("/pages/recently-created") }}">{{ trans('entities.recently_created_pages') }}</a></h4>
                <div id="recently-created-pages">
                    @include('partials/entity-list', [
                    'entities' => $recentlyCreatedPages,
                    'style' => 'compact',
                    'emptyText' => trans('entities.no_pages_recently_created')
                    ])
                </div>

                <h4><a class="no-color" href="{{ baseUrl("/pages/recently-updated") }}">{{ trans('entities.recently_updated_pages') }}</a></h4>
                <div id="recently-updated-pages">
                    @include('partials/entity-list', [
                    'entities' => $recentlyUpdatedPages,
                    'style' => 'compact',
                    'emptyText' => trans('entities.no_pages_recently_updated')
                    ])
                </div>
            </div>

            <div class="col-sm-4" id="recent-activity">
                <h4>{{ trans('entities.recent_activity') }}</h4>
                @include('partials/activity-list', ['activity' => $activity])
            </div>

        </div>
    </div>


@stop