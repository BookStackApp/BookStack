@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container">
        <h1>Create New Role</h1>

        <form action="{{ baseUrl("/settings/roles/new") }}" method="POST">
            @include('settings/roles/form')
        </form>
    </div>

@stop
