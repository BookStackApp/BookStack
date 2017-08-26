@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'roles'])
@stop

@section('body')

    <form action="{{ baseUrl("/settings/roles/new") }}" method="POST">
        <div class="container">
            <div class="row">
                @include('settings/roles/form', ['title' => trans('settings.role_create'), 'icon' => 'plus'])
            </div>
        </div>
    </form>

@stop
