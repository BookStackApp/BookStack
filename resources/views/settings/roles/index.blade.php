@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container">

        <h1>User Roles</h1>
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
