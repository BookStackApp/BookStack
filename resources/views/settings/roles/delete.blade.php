@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container small" ng-non-bindable>
        <h1>Delete Role</h1>
        <p>This will delete the role with the name '{{$role->display_name}}'.</p>

        <form action="/settings/roles/delete/{{$role->id}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">

            @if($role->users->count() > 0)
            <div class="form-group">
                    <p>This role has {{$role->users->count()}} users assigned to it. If you would like to migrate the users from this role select a new role below.</p>
                    @include('form/role-select', ['options' => $roles, 'name' => 'migration_role_id'])
            </div>
            @endif

            <p class="text-neg">Are you sure you want to delete this role?</p>
            <a href="/settings/roles/{{ $role->id }}" class="button">Cancel</a>
            <button type="submit" class="button neg">Confirm</button>
        </form>
    </div>

@stop
