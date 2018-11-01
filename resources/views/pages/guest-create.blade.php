@extends('base')

@section('content')

    <div class="container small">
        <h1>{{ trans('entities.pages_new') }}</h1>
        <form action="{{  $parent->getUrl('/create-guest-page') }}" method="POST">

            {!! csrf_field() !!}

            <div class="form-group title-input">
                <label for="name">{{ trans('entities.pages_name') }}</label>
                @include('form/text', ['name' => 'name'])
            </div>

            <div class="form-group">
                <a href="{{ $parent->getUrl() }}" class="button muted">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button pos">{{ trans('common.continue') }}</button>
            </div>

        </form>
    </div>


@stop