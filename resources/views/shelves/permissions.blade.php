@extends('layouts.simple')

@section('body')

    <div class="container">

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
            @include('form.entity-permissions', ['model' => $shelf, 'title' => trans('entities.shelves_permissions')])
        </div>

        <div class="card content-wrap auto-height flex-container-row items-center gap-x-xl wrap">
            <div class="flex">
                <h2 class="list-heading">{{ trans('entities.shelves_copy_permissions_to_books') }}</h2>
                <p>{{ trans('entities.shelves_copy_permissions_explain') }}</p>
            </div>
            <form action="{{ $shelf->getUrl('/copy-permissions') }}" method="post" class="flex text-right">
                {{ csrf_field() }}
                <button class="button">{{ trans('entities.shelves_copy_permissions') }}</button>
            </form>
        </div>
    </div>

@stop
