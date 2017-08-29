@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'roles'])
@stop

@section('body')
    <div class="container small" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3><i class="zmdi zmdi-delete"></i> {{ trans('settings.role_delete') }}</h3>
            <div class="body">
                <p>{{ trans('settings.role_delete_confirm', ['roleName' => $role->display_name]) }}</p>

                <form action="{{ baseUrl("/settings/roles/delete/{$role->id}") }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">

                    @if($role->users->count() > 0)
                        <div class="form-group">
                            <p>{{ trans('settings.role_delete_users_assigned', ['userCount' => $role->users->count()]) }}</p>
                            @include('form/role-select', ['options' => $roles, 'name' => 'migration_role_id'])
                        </div>
                    @endif

                    <p class="text-neg">{{ trans('settings.role_delete_sure') }}</p>
                    <div class="form-group">
                        <a href="{{ baseUrl("/settings/roles/{$role->id}") }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@stop
