@extends('layouts.simple')

@section('body')

    <div class="container small">
        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'webhooks'])
        </div>

        <form action="{{ url("/settings/webhooks/{$webhook->id}") }}" method="POST">
            {!! method_field('PUT') !!}
            @include('settings.webhooks.parts.form', ['model' => $webhook, 'title' => trans('settings.webhooks_edit')])
        </form>
    </div>

@stop
