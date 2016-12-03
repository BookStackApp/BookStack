@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('pages._breadcrumbs', ['page' => $page])
                </div>
            </div>
        </div>
    </div>

    <div class="container small" ng-non-bindable>
        <h1>{{ $page->draft ? trans('entities.pages_delete_draft') : trans('entities.pages_delete') }}</h1>
        <p class="text-neg">{{ $page->draft ? trans('entities.pages_delete_draft_confirm'): trans('entities.pages_delete_confirm') }}</p>

        <form action="{{ $page->getUrl() }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{ $page->getUrl() }}" class="button primary">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
        </form>
    </div>

@stop