@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h1>Edit Role <small> {{ $role->display_name }}</small></h1>
            </div>
            <div class="col-sm-6">
                <p></p>
                <a href="/settings/roles/delete/{{ $role->id }}" class="button neg float right">Delete Role</a>
            </div>
        </div>

        <form action="/settings/roles/{{ $role->id }}" method="POST">
            <input type="hidden" name="_method" value="PUT">
            @include('settings/roles/form', ['model' => $role])
        </form>
    </div>

@stop
