@extends('simple-layout')

@section('body')

    <div class="container">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'roles'])
        </div>

        <form action="{{ baseUrl("/settings/roles/new") }}" method="POST">
            @include('settings.roles.form', ['title' => trans('settings.role_create')])
        </form>
    </div>

@stop
