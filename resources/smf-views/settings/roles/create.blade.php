@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'roles'])

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('settings.role_create') }}</h1>

            <form action="{{ url("/settings/roles/new") }}" method="POST">
                {{ csrf_field() }}

                @include('settings.roles.parts.form', ['role' => $role ?? null])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/roles") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('settings.role_save') }}</button>
                </div>
            </form>

        </div>
    </div>

@stop
