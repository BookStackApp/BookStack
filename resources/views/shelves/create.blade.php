@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                '/shelves' => trans('entities.shelves'),
                '/create-shelf' => trans('entities.shelves_create')
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.shelves_create') }}</h1>
            <form action="{{ baseUrl("/shelves") }}" method="POST" enctype="multipart/form-data">
                @include('shelves.form', ['shelf' => null, 'books' => $books])
            </form>
        </div>

    </div>

    @include('components.image-manager', ['imageType' => 'cover'])

@stop