@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'webhooks'])

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.webhooks_create') }}</h1>

            <form action="{{ url("/settings/webhooks/create") }}" method="POST">
                {!! csrf_field() !!}
                @include('settings.webhooks.parts.form', ['title' => trans('settings.webhooks_create')])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/webhooks") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('settings.webhooks_save') }}</button>
                </div>
            </form>
        </div>

        @include('settings.webhooks.parts.format-example')
    </div>

@stop
