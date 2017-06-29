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

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('entities.books_edit') }}</h1>
        <form action="{{ $book->getUrl() }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            @include('books/form', ['model' => $book])
        </form>
    </div>

@stop