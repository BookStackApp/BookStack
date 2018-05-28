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
            <h3>@icon('delete') {{ $page->draft ? trans('entities.pages_delete_draft') : trans('entities.pages_delete') }}</h3>
            <div class="body">
                <p class="text-neg">{{ $page->draft ? trans('entities.pages_delete_draft_confirm'): trans('entities.pages_delete_confirm') }}</p>

                <form action="{{ $page->getUrl() }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="form-group">
                        <a href="{{ $page->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop