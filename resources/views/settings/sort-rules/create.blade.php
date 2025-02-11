@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'settings'])

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.sort_rule_create') }}</h1>

            <form action="{{ url("/settings/sorting/rules") }}" method="POST">
                {{ csrf_field() }}
                @include('settings.sort-rules.parts.form', ['model' => null])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/sorting") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop
