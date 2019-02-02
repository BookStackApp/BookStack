@extends('simple-layout')

@section('body')

    <div class="container">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/permissions') => trans('entities.chapters_permissions')
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.chapters_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $chapter])
        </div>
    </div>

@stop
