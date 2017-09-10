@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'roles'])
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3><i class="zmdi zmdi-lock-open"></i> {{ trans('settings.role_user_roles') }}</h3>
            <div class="body">
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

                <div class="form-group">
                    <a href="{{ baseUrl("/settings/roles/new") }}" class="button pos">{{ trans('settings.role_create') }}</a>
                </div>
            </div>
        </div>
    </div>

@stop
