@extends('base')

@section('content')

    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-sm-7">

                <div class="padded-top large"></div>

                <div class="row">
                    <div class="col-md-7">
                        <div class="clearfix">
                            <div class="padded-right float left">
                                <img class="avatar square huge" src="{{$user->getAvatar(120)}}" alt="{{ $user->name }}">
                            </div>
                            <div>
                                <h3 style="margin-top: 0;">{{ $user->name }}</h3>
                                <p class="text-muted">
                                    User for {{ $user->created_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-bigger">
                        <div class="text-muted">Created Content</div>
                        <div class="text-book">
                            <i class="zmdi zmdi-book zmdi-hc-fw"></i> {{ $assetCounts['books'] }} {{ str_plural('Book', $assetCounts['books']) }}
                        </div>
                        <div class="text-chapter">
                            <i class="zmdi zmdi-collection-bookmark zmdi-hc-fw"></i> {{ $assetCounts['chapters'] }} {{ str_plural('Chapter', $assetCounts['chapters']) }}
                        </div>
                        <div class="text-page">
                            <i class="zmdi zmdi-file-text zmdi-hc-fw"></i> {{ $assetCounts['pages'] }} {{ str_plural('Page', $assetCounts['pages']) }}
                        </div>
                    </div>
                </div>


                <hr class="even">

                <h3>Recently Created Pages</h3>
                @if (count($recentlyCreated['pages']) > 0)
                    @include('partials/entity-list', ['entities' => $recentlyCreated['pages']])
                @else
                    <p class="text-muted">{{ $user->name }} has not created any pages</p>
                @endif

                <hr class="even">

                <h3>Recently Created Chapters</h3>
                @if (count($recentlyCreated['chapters']) > 0)
                    @include('partials/entity-list', ['entities' => $recentlyCreated['chapters']])
                @else
                    <p class="text-muted">{{ $user->name }} has not created any chapters</p>
                @endif

                <hr class="even">

                <h3>Recently Created Books</h3>
                @if (count($recentlyCreated['books']) > 0)
                    @include('partials/entity-list', ['entities' => $recentlyCreated['books']])
                @else
                    <p class="text-muted">{{ $user->name }} has not created any books</p>
                @endif
            </div>

            <div class="col-sm-4 col-sm-offset-1">
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => $activity])
            </div>

        </div>
    </div>


@stop