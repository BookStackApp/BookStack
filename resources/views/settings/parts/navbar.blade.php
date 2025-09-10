
<nav class="active-link-list py-m flex-container-row justify-center wrap">
    @if(userCan(\BookStack\Permissions\Permission::SettingsManage))
        <a href="{{ url('/settings') }}" @if($selected == 'settings') class="active" @endif>@icon('settings'){{ trans('settings.settings') }}</a>
        <a href="{{ url('/settings/maintenance') }}" @if($selected == 'maintenance') class="active" @endif>@icon('spanner'){{ trans('settings.maint') }}</a>
    @endif
    @if(userCan(\BookStack\Permissions\Permission::SettingsManage) && userCan(\BookStack\Permissions\Permission::UsersManage))
        <a href="{{ url('/settings/audit') }}" @if($selected == 'audit') class="active" @endif>@icon('open-book'){{ trans('settings.audit') }}</a>
    @endif
    @if(userCan(\BookStack\Permissions\Permission::UsersManage))
        <a href="{{ url('/settings/users') }}" @if($selected == 'users') class="active" @endif>@icon('users'){{ trans('settings.users') }}</a>
    @endif
    @if(userCan(\BookStack\Permissions\Permission::UserRolesManage))
        <a href="{{ url('/settings/roles') }}" @if($selected == 'roles') class="active" @endif>@icon('lock-open'){{ trans('settings.roles') }}</a>
    @endif
    @if(userCan(\BookStack\Permissions\Permission::SettingsManage))
        <a href="{{ url('/settings/webhooks') }}" @if($selected == 'webhooks') class="active" @endif>@icon('webhooks'){{ trans('settings.webhooks') }}</a>
    @endif
</nav>