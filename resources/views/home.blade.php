@extends('base')

@section('content')

    <div class="container" ng-non-bindable>
        <div class="row">

            <div class="col-md-7">
                @if($signedIn)
                    <h2>My Recently Viewed</h2>
                @else
                    <h2>Recent Books</h2>
                @endif
                @include('partials/entity-list', ['entities' => $recents])
            </div>

            <div class="col-md-4 col-md-offset-1" id="recent-activity">
                <div class="margin-top large">&nbsp;</div>
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => $activity])
            </div>

        </div>
    </div>


@stop