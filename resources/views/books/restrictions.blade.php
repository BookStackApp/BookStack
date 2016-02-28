@extends('base')

@section('content')

    <div class="container" ng-non-bindable>
        <h1>Book Restrictions</h1>
        @include('form/restriction-form', ['model' => $book])
    </div>

@stop
