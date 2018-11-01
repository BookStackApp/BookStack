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
            <h3>@icon('delete') {{ trans('entities.shelves_delete') }}</h3>
            <div class="body">
                <p>{{ trans('entities.shelves_delete_explain', ['name' => $shelf->name]) }}</p>
                <p class="text-neg">{{ trans('entities.shelves_delete_confirmation') }}</p>

                <form action="{{ $shelf->getUrl() }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">

                    <a href="{{ $shelf->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                </form>
            </div>
        </div>
    </div>

@stop