@extends('layouts.simple')

@section('body')
    <div class="container small">
        <div class="my-s">
            @if (isset($recordshelf))
                @include('entities.breadcrumbs', ['crumbs' => [
                    $recordshelf,
                    $recordshelf->getUrl('/create-record') => [
                        'text' => trans('entities.records_create'),
                        'icon' => 'add'
                    ]
                ]])
            @else
                @include('entities.breadcrumbs', ['crumbs' => [
                    '/records' => [
                        'text' => trans('entities.records'),
                        'icon' => 'book'
                    ],
                    '/create-record' => [
                        'text' => trans('entities.records_create'),
                        'icon' => 'add'
                    ]
                ]])
            @endif
        </div>

        <main class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.records_create') }}</h1>
            <form action="{{ $recordshelf?->getUrl('/create-record') ?? url('/records') }}" method="POST" enctype="multipart/form-data">
                @include('records.parts.form', [
                    'returnLocation' => $recordshelf?->getUrl() ?? url('/records')
                ])
            </form>
        </main>
    </div>

@stop
