@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/references') => [
                    'text' => trans('entities.references'),
                    'icon' => 'reference',
                ]
            ]])
        </div>

        @include('entities.references', ['references' => $references])
    </div>

@stop
