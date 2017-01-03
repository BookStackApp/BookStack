@extends('base')

@section('content')

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('entities.chapters_create') }}</h1>
        <form action="{{ $book->getUrl('/chapter/create') }}" method="POST">
            @include('chapters/form')
        </form>
    </div>

@stop