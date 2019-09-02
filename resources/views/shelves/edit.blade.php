@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $shelf,
                $shelf->getUrl('/edit') => [
                    'text' => trans('entities.shelves_edit'),
                    'icon' => 'edit',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.shelves_edit') }}</h1>
            <form action="{{ $shelf->getUrl() }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                @include('shelves.form', ['model' => $shelf])
            </form>
        </main>
    </div>

@stop