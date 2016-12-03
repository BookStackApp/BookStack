@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('books._breadcrumbs', ['book' => $book])
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable>
        <h1>{{ trans('entities.books_permissions') }}</h1>
        @include('form/restriction-form', ['model' => $book])
    </div>

@stop
