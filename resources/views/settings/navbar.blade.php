
<div class="faded-small toolbar">
    <div class="container">
        <div class="row">
            <div class="col-md-12 setting-nav nav-tabs">
                @if($currentUser->can('settings-manage'))
                    <a href="{{ baseUrl('/settings') }}" @if($selected == 'settings') class="selected text-button" @endif><i class="zmdi zmdi-settings"></i>{{ trans('settings.settings') }}</a>
                @endif
                @if($currentUser->can('users-manage'))
                    <a href="{{ baseUrl('/settings/users') }}" @if($selected == 'users') class="selected text-button" @endif><i class="zmdi zmdi-accounts"></i>{{ trans('settings.users') }}</a>
                @endif
                @if($currentUser->can('user-roles-manage'))
                    <a href="{{ baseUrl('/settings/roles') }}" @if($selected == 'roles') class="selected text-button" @endif><i class="zmdi zmdi-lock-open"></i>{{ trans('settings.roles') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>