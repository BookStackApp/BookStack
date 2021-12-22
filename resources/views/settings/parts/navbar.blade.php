
<nav class="active-link-list">
    @if(userCan('settings-manage'))
        <a href="{{ url('/settings') }}" @if($selected == 'settings') class="active" @endif>@icon('settings'){{ trans('settings.settings') }}</a>
        <a href="{{ url('/settings/maintenance') }}" @if($selected == 'maintenance') class="active" @endif>@icon('spanner'){{ trans('settings.maint') }}</a>
    @endif
    @if(userCan('settings-manage') && userCan('users-manage'))
        <a href="{{ url('/settings/audit') }}" @if($selected == 'audit') class="active" @endif>@icon('open-book'){{ trans('settings.audit') }}</a>
    @endif
    @if(userCan('users-manage'))
        <a href="{{ url('/settings/users') }}" @if($selected == 'users') class="active" @endif>@icon('users'){{ trans('settings.users') }}</a>
    @endif
    @if(userCan('user-roles-manage'))
        <a href="{{ url('/settings/roles') }}" @if($selected == 'roles') class="active" @endif>@icon('lock-open'){{ trans('settings.roles') }}</a>
    @endif
    @if(userCan('settings-manage'))
        <a href="{{ url('/settings/webhooks') }}" @if($selected == 'webhooks') class="active" @endif>@icon('webhooks'){{ trans('settings.webhooks') }}</a>
    @endif
</nav>