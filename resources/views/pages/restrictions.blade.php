@extends('base')

@section('content')

    <div class="container" ng-non-bindable>
        <h1>Page Restrictions</h1>
        @include('form/restriction-form', ['model' => $page])
    </div>

@stop
