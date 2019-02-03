
<div class="pt-m">
    <label class="setting-list-label">{{ trans('settings.users_details') }}</label>
    <p class="small">{{ trans('settings.users_details_desc') }}</p>
    <div class="grid half mt-m large-gap">
        <div>
            <label for="name">{{ trans('auth.name') }}</label>
            @include('form.text', ['name' => 'name'])
        </div>
        <div>
            <label for="email">{{ trans('auth.email') }}</label>
            @include('form.text', ['name' => 'email'])
        </div>
    </div>
</div>


@if(userCan('users-manage'))
    <div>
        <label for="role" class="setting-list-label">{{ trans('settings.users_role') }}</label>
        <p class="small">{{ trans('settings.users_role_desc') }}</p>
        <div class="mt-m">
            @include('form/role-checkboxes', ['name' => 'roles', 'roles' => $roles])
        </div>
    </div>
@endif


<div>
    <label class="setting-list-label">{{ trans('settings.users_password') }}</label>
    <p class="small">{{ trans('settings.users_password_desc') }}</p>
    @if(isset($model))
        <p class="small">
            {{ trans('settings.users_password_warning') }}
        </p>
    @endif
    <div class="grid half mt-m large-gap">
        <div>
            <label for="password">{{ trans('auth.password') }}</label>
            @include('form.password', ['name' => 'password'])
        </div>
        <div>
            <label for="password-confirm">{{ trans('auth.password_confirm') }}</label>
            @include('form.password', ['name' => 'password-confirm'])
        </div>
    </div>
</div>