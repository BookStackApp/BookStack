@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('chapters._breadcrumbs', ['chapter' => $chapter])
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>{{ trans('entities.chapters_move') }}</h1>

        <form action="{{ $chapter->getUrl('/move') }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">

            @include('partials/entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book'])

            <a href="{{ $chapter->getUrl() }}" class="button muted">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button pos">{{ trans('entities.chapters_move') }}</button>
        </form>
    </div>

@stop
