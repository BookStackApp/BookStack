@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{$book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable>
        <h1>Book Permissions</h1>
        @include('form/restriction-form', ['model' => $book])
    </div>

@stop
