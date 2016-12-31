@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container small">

        <div class="row action-header">
            <div class="col-sm-8">
                <h1>{{ trans('settings.role_user_roles') }}</h1>
            </div>
            <div class="col-sm-4">
                <p></p>
                <a href="{{ baseUrl("/settings/roles/new") }}" class="button float right pos"><i class="zmdi zmdi-lock-open"></i>{{ trans('settings.role_create') }}</a>
            </div>
        </div>

        <table class="table">
            <tr>
                <th>{{ trans('settings.role_name') }}</th>
                <th></th>
                <th class="text-center">{{ trans('settings.users') }}</th>
            </tr>
            @foreach($roles as $role)
                <tr>
                    <td><a href="{{ baseUrl("/settings/roles/{$role->id}") }}">{{ $role->display_name }}</a></td>
                    <td>{{ $role->description }}</td>
                    <td class="text-center">{{ $role->users->count() }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@stop
