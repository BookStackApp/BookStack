@extends('base')

@section('content')

    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-sm-8">

                <div class="padded-top large"></div>
                <img class="" src="{{$user->getAvatar(120)}}" alt="{{ $user->name }}">
                <h3>{{ $user->name }}</h3>
                <p class="text-muted">
                    User for {{ $user->created_at->diffForHumans(null, true) }}
                </p>

            </div>

            <div class="col-sm-4">
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => $activity])
            </div>

        </div>
    </div>


@stop