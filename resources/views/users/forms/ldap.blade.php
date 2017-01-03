<div class="form-group">
    <label for="name">{{ trans('auth.name') }}</label>
    @include('form.text', ['name' => 'name'])
</div>

@if(userCan('users-manage'))
<div class="form-group">
    <label for="email">{{ trans('auth.email') }}</label>
    @include('form.text', ['name' => 'email'])
</div>
@endif

@if(userCan('users-manage'))
    <div class="form-group">
        <label for="role">{{ trans('settings.users_role') }}</label>
        @include('form/role-checkboxes', ['name' => 'roles', 'roles' => $roles])
    </div>
@endif

@if(userCan('users-manage'))
    <div class="form-group">
        <label for="external_auth_id">{{ trans('settings.users_external_auth_id') }}</label>
        @include('form.text', ['name' => 'external_auth_id'])
    </div>
@endif

<div class="form-group">
    <a href="{{ baseUrl("/settings/users") }}" class="button muted">{{ trans('common.cancel') }}</a>
    <button class="button pos" type="submit">{{ trans('common.save') }}</button>
</div>