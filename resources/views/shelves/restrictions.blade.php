@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('shelves._breadcrumbs', ['shelf' => $shelf])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('lock') {{ trans('entities.shelves_permissions') }}</h3>
            <div class="body">
                @include('form/restriction-form', ['model' => $shelf])
            </div>
        </div>

        <p>&nbsp;</p>

        <div class="card">
            <h3>@icon('copy') {{ trans('entities.shelves_copy_permissions_to_books') }}</h3>
            <div class="body">
                <p>{{ trans('entities.shelves_copy_permissions_explain') }}</p>
                <form action="{{ $shelf->getUrl('/copy-permissions') }}" method="post" class="text-right">
                    {{ csrf_field() }}
                    <button class="button">{{ trans('entities.shelves_copy_permissions') }}</button>
                </form>
            </div>
        </div>
    </div>

@stop
