@extends('layouts.simple')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/permissions') => [
                    'text' => trans('entities.chapters_permissions'),
                    'icon' => 'lock',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.chapters_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $chapter])
        </main>
    </div>

@stop
