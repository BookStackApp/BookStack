@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'roles'])
        </div>

        <form action="{{ url("/settings/roles/new") }}" method="POST">
            @include('settings.roles.parts.form', ['title' => trans('settings.role_create')])
        </form>
    </div>

@stop
