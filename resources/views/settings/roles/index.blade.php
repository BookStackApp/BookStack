@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container small">

        <h1>User Roles</h1>

        <p>
            <a href="/settings/roles/new" class="text-pos"><i class="zmdi zmdi-lock-open"></i>Add new role</a>
        </p>

        <table class="table">
            <tr>
                <th>Role Name</th>
                <th></th>
                <th class="text-right">Users</th>
            </tr>
            @foreach($roles as $role)
                <tr>
                    <td><a href="/settings/roles/{{ $role->id }}">{{ $role->display_name }}</a></td>
                    <td>{{ $role->description }}</td>
                    <td class="text-right">{{ $role->users->count() }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@stop
