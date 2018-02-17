@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('chapters._breadcrumbs', ['chapter' => $chapter])
    </div>
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('delete') {{ trans('entities.chapters_delete') }}</h3>

            <div class="body">
                <p>{{ trans('entities.chapters_delete_explain', ['chapterName' => $chapter->name]) }}</p>
                <p class="text-neg">{{ trans('entities.chapters_delete_confirm') }}</p>

                <form action="{{ $chapter->getUrl() }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <a href="{{ $chapter->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
                </form>
            </div>
        </div>
    </div>

@stop