<div class="form-group">
    <label for="name">{{ trans('auth.name') }}</label>
    @include('form.text', ['name' => 'name'])
</div>

<div class="form-group">
    <label for="email">{{ trans('auth.email') }}</label>
    @include('form.text', ['name' => 'email'])
</div>

@if(userCan('users-manage'))
    <div class="form-group">
        <label for="role">{{ trans('settings.users_role') }}</label>
        @include('form/role-checkboxes', ['name' => 'roles', 'roles' => $roles])
    </div>
@endif

@if(isset($model))
    <div class="form-group">
        <span class="text-muted">
            {{ trans('settings.users_password_warning') }}
        </span>
    </div>
@endif

<div class="form-group">
    <label for="password">{{ trans('auth.password') }}</label>
    @include('form.password', ['name' => 'password'])
</div>

<div class="form-group">
    <label for="password-confirm">{{ trans('auth.password_confirm') }}</label>
    @include('form.password', ['name' => 'password-confirm'])
</div>

<div class="form-group">
    <a href="{{ baseUrl("/settings/users") }}" class="button muted">{{ trans('common.cancel') }}</a>
    <button class="button pos" type="submit">{{ trans('common.save') }}</button>
</div>

