@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('pages._breadcrumbs', ['page' => $page])
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>{{ trans('entities.pages_move') }}</h1>

        <form action="{{ $page->getUrl('/move') }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">

            @include('components.entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book,chapter'])

            <a href="{{ $page->getUrl() }}" class="button muted">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button pos">{{ trans('entities.pages_move') }}</button>
        </form>
    </div>

@stop
