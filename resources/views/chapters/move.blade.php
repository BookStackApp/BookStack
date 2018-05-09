@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('chapters._breadcrumbs', ['chapter' => $chapter])
    </div>
@stop

@section('body')

    <div class="container small">

        <div class="card">
            <h3>@icon('folder') {{ trans('entities.chapters_move') }}</h3>
            <div class="body">
                <form action="{{ $chapter->getUrl('/move') }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="PUT">

                    @include('components.entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book', 'entityPermission' => 'chapter-create'])

                    <div class="form-group text-right">
                        <a href="{{ $chapter->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button pos">{{ trans('entities.chapters_move') }}</button>
                    </div>
                </form>
            </div>
        </div>



    </div>

@stop
