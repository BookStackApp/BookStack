<div class="item-list-row flex-container-row py-xs items-center">
    <div class="py-xs px-m flex-2">
        <a href="{{ url("/settings/roles/{$role->id}") }}">{{ $role->display_name }}</a><br>
        @if($role->mfa_enforced)
            <small title="{{ trans('settings.role_mfa_enforced') }}">@icon('lock') </small>
        @endif
        <small>{{ $role->description }}</small>
    </div>
    <div class="text-right flex py-xs px-m text-muted">
        {{ trans_choice('settings.roles_x_users_assigned', $role->users_count, ['count' => $role->users_count]) }}
        <br>
        {{ trans_choice('settings.roles_x_permissions_provided', $role->permissions_count, ['count' => $role->permissions_count]) }}
    </div>
</div>