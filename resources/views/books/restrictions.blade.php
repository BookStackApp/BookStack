@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('books._breadcrumbs', ['book' => $book])
    </div>
@stop

@section('body')

    <div class="container" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3><@icon('lock') {{ trans('entities.books_permissions') }}</h3>
            <div class="body">
                @include('form/restriction-form', ['model' => $book])
            </div>
        </div>
    </div>

@stop
