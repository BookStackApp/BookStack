
<div class="col-md-12 setting-nav nav-tabs">
    @if($currentUser->can('settings-manage'))
        <a href="{{ baseUrl('/settings') }}" @if($selected == 'settings') class="selected text-button" @endif>@icon('settings'){{ trans('settings.settings') }}</a>
    @endif
    @if($currentUser->can('users-manage'))
        <a href="{{ baseUrl('/settings/users') }}" @if($selected == 'users') class="selected text-button" @endif>@icon('users'){{ trans('settings.users') }}</a>
    @endif
    @if($currentUser->can('user-roles-manage'))
        <a href="{{ baseUrl('/settings/roles') }}" @if($selected == 'roles') class="selected text-button" @endif>@icon('lock-open'){{ trans('settings.roles') }}</a>
    @endif
</div>