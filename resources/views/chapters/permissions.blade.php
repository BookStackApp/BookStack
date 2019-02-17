@extends('simple-layout')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/permissions') => [
                    'text' => trans('entities.chapters_permissions'),
                    'icon' => 'lock',
                ]
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.chapters_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $chapter])
        </div>
    </div>

@stop
