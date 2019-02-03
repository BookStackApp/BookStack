
@if($authMethod === 'system' && $user->system_name == 'public')
    <p class="mb-none text-secondary">{{ trans('settings.users_system_public') }}</p>
@endif

<div class="pt-m">
    <label class="setting-list-label">{{ trans('settings.users_details') }}</label>
    @if($authMethod === 'standard')
        <p class="small">{{ trans('settings.users_details_desc') }}</p>
    @endif
    @if($authMethod === 'ldap' || $authMethod === 'system')
        <p class="small">{{ trans('settings.users_details_desc_no_email') }}</p>
    @endif
    <div class="grid half mt-m large-gap">
        <div>
            <label for="name">{{ trans('auth.name') }}</label>
            @include('form.text', ['name' => 'name'])
        </div>
        <div>
            @if($authMethod !== 'ldap' || userCan('users-manage'))
                <label for="email">{{ trans('auth.email') }}</label>
                @include('form.text', ['name' => 'email'])
            @endif
        </div>
    </div>
</div>

@if($authMethod === 'ldap' && userCan('users-manage'))
    <div class="grid half large-gap v-center">
        <div>
            <label class="setting-list-label">{{ trans('settings.users_external_auth_id') }}</label>
            <p class="small">{{ trans('settings.users_external_auth_id_desc') }}</p>
        </div>
        <div>
            @include('form.text', ['name' => 'external_auth_id'])
        </div>
    </div>
@endif

@if(userCan('users-manage'))
    <div>
        <label for="role" class="setting-list-label">{{ trans('settings.users_role') }}</label>
        <p class="small">{{ trans('settings.users_role_desc') }}</p>
        <div class="mt-m">
            @include('form/role-checkboxes', ['name' => 'roles', 'roles' => $roles])
        </div>
    </div>
@endif

@if($authMethod === 'standard')
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
@endif