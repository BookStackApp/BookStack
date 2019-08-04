@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'roles'])
        </div>

        <form action="{{ url("/settings/roles/new") }}" method="POST">
            @include('settings.roles.form', ['title' => trans('settings.role_create')])
        </form>
    </div>

@stop
