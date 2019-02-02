@extends('simple-layout')

@section('body')

    <div class="container">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/permissions') => trans('entities.pages_permissions')
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.pages_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $page])
        </div>
    </div>

@stop
