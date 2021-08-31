@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $shelf,
                $shelf->getUrl('/permissions') => [
                    'text' => trans('entities.shelves_permissions'),
                    'icon' => 'lock',
                ]
            ]])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('entities.shelves_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $shelf])
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('entities.shelves_copy_permissions_to_books') }}</h2>
            <p>{{ trans('entities.shelves_copy_permissions_explain') }}</p>
            <form action="{{ $shelf->getUrl('/copy-permissions') }}" method="post" class="text-right">
                {{ csrf_field() }}
                <button class="button">{{ trans('entities.shelves_copy_permissions') }}</button>
            </form>
        </div>
    </div>

@stop
