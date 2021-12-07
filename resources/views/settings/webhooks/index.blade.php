@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'webhooks'])
        </div>

        <div class="card content-wrap auto-height">

            <div class="grid half v-center">
                <h1 class="list-heading">{{ trans('settings.webhooks') }}</h1>

                <div class="text-right">
                    <a href="{{ url("/settings/webhooks/new") }}" class="button outline">{{ trans('settings.webhooks_create') }}</a>
                </div>
            </div>


        </div>
    </div>

@stop
