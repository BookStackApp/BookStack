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

        <main class="card content-wrap auto-height">
            @include('form.entity-permissions', ['model' => $chapter, 'title' => trans('entities.chapters_permissions')])
        </main>
    </div>

@stop
