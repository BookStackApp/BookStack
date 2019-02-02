@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/move') => trans('entities.chapters_move')
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.chapters_move') }}</h1>

            <form action="{{ $chapter->getUrl('/move') }}" method="POST">

                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="PUT">

                @include('components.entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book', 'entityPermission' => 'chapter-create'])

                <div class="form-group text-right">
                    <a href="{{ $chapter->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button primary">{{ trans('entities.chapters_move') }}</button>
                </div>
            </form>

        </div>



    </div>

@stop
