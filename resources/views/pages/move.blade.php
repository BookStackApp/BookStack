@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('pages._breadcrumbs', ['page' => $page])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('folder') {{ trans('entities.pages_move') }}</h3>
            <div class="body">
                <form action="{{ $page->getUrl('/move') }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="PUT">

                    @include('components.entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book,chapter', 'entityPermission' => 'page-create'])

                    <div class="form-group text-right">
                        <a href="{{ $page->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button pos">{{ trans('entities.pages_move') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
