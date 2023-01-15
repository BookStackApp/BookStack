@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/delete') => [
                    'text' => trans('entities.chapters_delete'),
                    'icon' => 'delete',
                ]
            ]])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('entities.chapters_delete') }}</h1>
            <p>{{ trans('entities.chapters_delete_explain', ['chapterName' => $chapter->name]) }}</p>
            <p class="text-neg"><strong>{{ trans('entities.chapters_delete_confirm') }}</strong></p>

            <form action="{{ $chapter->getUrl() }}" method="POST">

                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="DELETE">

                <div class="text-right">
                    <a href="{{ $chapter->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop