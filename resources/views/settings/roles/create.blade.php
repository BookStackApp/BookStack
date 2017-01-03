@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container">
        <h1>{{ trans('settings.role_create') }}</h1>

        <form action="{{ baseUrl("/settings/roles/new") }}" method="POST">
            @include('settings/roles/form')
        </form>
    </div>

@stop
