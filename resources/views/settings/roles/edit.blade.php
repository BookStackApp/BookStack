@extends('layouts.simple')

@section('body')

    <div class="container small">
        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'roles'])
        </div>

        <form action="{{ url("/settings/roles/{$role->id}") }}" method="POST">
            <input type="hidden" name="_method" value="PUT">
            @include('settings.roles.parts.form', ['model' => $role, 'title' => trans('settings.role_edit'), 'icon' => 'edit'])
        </form>
    </div>

@stop
