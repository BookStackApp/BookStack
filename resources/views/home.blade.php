@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 faded">
                    <div class="action-buttons text-left">
                        <a data-action="expand-entity-list-details" class="text-primary text-button"><i class="zmdi zmdi-wrap-text"></i>Toggle Details</a>
                    </div>
                </div>
                <div class="col-sm-8 faded">
                    <div class="action-buttons">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" ng-non-bindable>
        <div class="row">

            <div class="col-sm-4">
                @if($signedIn)
                    <h3>My Recently Viewed</h3>
                @else
                    <h3>Recent Books</h3>
                @endif
                @include('partials/entity-list', ['entities' => $recents, 'style' => 'compact'])
            </div>

            <div class="col-sm-4">
                <h3><a class="no-color" href="/pages/recently-created">Recently Created Pages</a></h3>
                @include('partials/entity-list', ['entities' => $recentlyCreatedPages, 'style' => 'compact'])

                <h3><a class="no-color" href="/pages/recently-updated">Recently Updated Pages</a></h3>
                @include('partials/entity-list', ['entities' => $recentlyCreatedPages, 'style' => 'compact'])
            </div>

            <div class="col-sm-4" id="recent-activity">
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => $activity])
            </div>

        </div>
    </div>


@stop