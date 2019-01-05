@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-6 col-xs-1 faded">
        <div class="breadcrumbs">
            <a href="{{ $user->getProfileUrl() }}" class="text-button">@icon('user'){{ $user->name }}</a>
        </div>
    </div>
@stop

@section('sidebar')
    <div class="card" id="recent-activity">
        <h3>@icon('time') {{ trans('entities.recent_activity') }}</h3>
        @include('partials/activity-list', ['activity' => $activity])
    </div>
@stop

@section('body')

    <div class="container small">

        <div class="padded-top large"></div>

        <div class="row">
            <div class="col-md-7">
                <div class="clearfix">
                    <div class="padded-right float left">
                        <img class="avatar square huge" src="{{ $user->getAvatar(120) }}" alt="{{ $user->name }}">
                    </div>
                    <div>
                        <h3 style="margin-top: 0;">{{ $user->name }}</h3>
                        <p class="text-muted">
                            {{ trans('entities.profile_user_for_x', ['time' => $user->created_at->diffForHumans(null, true)]) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-bigger" id="content-counts">
                <div class="text-muted">{{ trans('entities.profile_created_content') }}</div>
                <a href="#book">
                    <div class="text-book">
                        @icon('book')  {{ trans_choice('entities.x_books', $assetCounts['books']) }}
                    </div>
                </a>
                <a href="#chapter">
                    <div class="text-chapter">
                        @icon('chapter') {{ trans_choice('entities.x_chapters', $assetCounts['chapters']) }}
                    </div>
                </a>
                <a href="#page">
                    <div class="text-page">
                        @icon('page') {{ trans_choice('entities.x_pages', $assetCounts['pages']) }}
                    </div>
                </a>
            </div>
        </div>


        <hr class="even">
        <a name="page"></a>
        <h3>{{ trans('entities.recently_created_pages') }}</h3>
        @if (count($recentlyCreated['pages']) > 0)
            @include('partials/entity-list', ['entities' => $recentlyCreated['pages']])
        @else
            <p class="text-muted">{{ trans('entities.profile_not_created_pages', ['userName' => $user->name]) }}</p>
        @endif

        <hr class="even">
        <a name="chapter"></a>
        <h3>{{ trans('entities.recently_created_chapters') }}</h3>
        @if (count($recentlyCreated['chapters']) > 0)
            @include('partials/entity-list', ['entities' => $recentlyCreated['chapters']])
        @else
            <p class="text-muted">{{ trans('entities.profile_not_created_chapters', ['userName' => $user->name]) }}</p>
        @endif

        <hr class="even">
        <a name="book"></a>
        <h3>{{ trans('entities.recently_created_books') }}</h3>
        @if (count($recentlyCreated['books']) > 0)
            @include('partials/entity-list', ['entities' => $recentlyCreated['books']])
        @else
            <p class="text-muted">{{ trans('entities.profile_not_created_books', ['userName' => $user->name]) }}</p>
        @endif
    </div>


@stop