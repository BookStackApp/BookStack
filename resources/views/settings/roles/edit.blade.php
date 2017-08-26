@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'roles'])
@stop

@section('body')

    <form action="{{ baseUrl("/settings/roles/{$role->id}") }}" method="POST">
        <input type="hidden" name="_method" value="PUT">
        <div class="container">
            <div class="row">
                @include('settings/roles/form', ['model' => $role, 'title' => trans('settings.role_edit'), 'icon' => 'edit'])
            </div>
        </div>
    </form>
@stop
