@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{$chapter->book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $chapter->book->getShortName() }}</a>
                        <span class="sep">&raquo;</span>
                        <a href="{{ $chapter->getUrl() }}" class="text-chapter text-button"><i class="zmdi zmdi-collection-bookmark"></i>{{$chapter->getShortName()}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" ng-non-bindable>
        <h1>Chapter Restrictions</h1>
        @include('form/restriction-form', ['model' => $chapter])
    </div>

@stop
