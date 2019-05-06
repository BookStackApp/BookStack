
<div class="active-link-list">
    @if($currentUser->can('settings-manage'))
        <a href="{{ baseUrl('/settings') }}" @if($selected == 'settings') class="active" @endif>@icon('settings'){{ trans('settings.settings') }}</a>
        <a href="{{ baseUrl('/settings/maintenance') }}" @if($selected == 'maintenance') class="active" @endif>@icon('spanner'){{ trans('settings.maint') }}</a>
    @endif
    @if($currentUser->can('users-manage'))
        <a href="{{ baseUrl('/settings/users') }}" @if($selected == 'users') class="active" @endif>@icon('users'){{ trans('settings.users') }}</a>
    @endif
    @if($currentUser->can('user-roles-manage'))
        <a href="{{ baseUrl('/settings/roles') }}" @if($selected == 'roles') class="active" @endif>@icon('lock-open'){{ trans('settings.roles') }}</a>
    @endif
</div>