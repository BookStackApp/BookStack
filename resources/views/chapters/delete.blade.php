@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('chapters._breadcrumbs', ['chapter' => $chapter])
                </div>
            </div>
        </div>
    </div>

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('entities.chapters_delete') }}</h1>
        <p>{{ trans('entities.chapters_delete_explain', ['chapterName' => $chapter->name]) }}</p>
        <p class="text-neg">{{ trans('entities.chapters_delete_confirm') }}</p>

        <form action="{{ $chapter->getUrl() }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{ $chapter->getUrl() }}" class="button primary">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
        </form>
    </div>

@stop