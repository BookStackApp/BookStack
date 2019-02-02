@extends('simple-layout')

{{--TODO - Looks strange--}}

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'roles'])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading"> {{ trans('settings.role_delete') }}</h1>

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

                <p class="text-neg">
                    <strong>{{ trans('settings.role_delete_sure') }}</strong>
                </p>

                <div class="form-group text-right">
                    <a href="{{ baseUrl("/settings/roles/{$role->id}") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button primary">{{ trans('common.confirm') }}</button>
                </div>
            </form>
        </div>

    </div>
@stop
