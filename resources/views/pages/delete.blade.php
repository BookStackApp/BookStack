@extends('simple-layout')


@section('body')

    <div class="container small">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/delete') => trans('entities.pages_delete')
            ]])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ $page->draft ? trans('entities.pages_delete_draft') : trans('entities.pages_delete') }}</h1>

            <p class="text-neg">
                <strong>
                    {{ $page->draft ? trans('entities.pages_delete_draft_confirm'): trans('entities.pages_delete_confirm') }}
                </strong>
            </p>

            {{--TODO - Looks strange--}}

            <form action="{{ $page->getUrl() }}" method="POST">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-group text-right">
                    <a href="{{ $page->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button primary">{{ trans('common.confirm') }}</button>
                </div>
            </form>

        </div>
    </div>

@stop