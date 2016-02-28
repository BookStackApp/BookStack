@extends('base')

@section('content')

    <div class="container" ng-non-bindable>
        <h1>Chapter Restrictions</h1>
        @include('form/restriction-form', ['model' => $chapter])
    </div>

@stop
