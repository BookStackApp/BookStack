<div class="item-list-row flex-container-row items-center wrap">
    <div class="flex py-s px-m min-width-s">
        <strong>{{ $title }}</strong> <br>
        <a href="#" refs="permissions-table@toggle-row" class="text-small text-link">{{ trans('common.toggle_all') }}</a>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.create') }}<br></small>
        @php $createAllAria = trans('settings.role_permission_aria', ['action' => trans('common.create'), 'resource' => strtolower($title), 'value' => trans('settings.role_all')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-create-all', 'label' => '', 'ariaLabel' => $createAllAria])
        @if($permissionPrefix === 'comment')<sup class="text-muted">2</sup>@endif
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.view') }}<br></small>
        <small class="faded">{{ trans('settings.role_controlled_by_asset') }}@if($permissionPrefix === 'image')<sup class="text-muted">1</sup>@endif</small>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.edit') }}<br></small>
        @php $editOwnAria = trans('settings.role_permission_aria', ['action' => trans('common.edit'), 'resource' => strtolower($title), 'value' => trans('settings.role_own')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-update-own', 'label' => trans('settings.role_own'), 'ariaLabel' => $editOwnAria])
        @if($permissionPrefix === 'comment')<sup class="text-muted">2</sup>@endif
        <br>
        @php $editAllAria = trans('settings.role_permission_aria', ['action' => trans('common.edit'), 'resource' => strtolower($title), 'value' => trans('settings.role_all')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-update-all', 'label' => trans('settings.role_all'), 'ariaLabel' => $editAllAria])
        @if($permissionPrefix === 'comment')<sup class="text-muted">2</sup>@endif
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.delete') }}<br></small>
        @php $deleteOwnAria = trans('settings.role_permission_aria', ['action' => trans('common.delete'), 'resource' => strtolower($title), 'value' => trans('settings.role_own')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-delete-own', 'label' => trans('settings.role_own'), 'ariaLabel' => $deleteOwnAria])
        <br>
        @php $deleteAllAria = trans('settings.role_permission_aria', ['action' => trans('common.delete'), 'resource' => strtolower($title), 'value' => trans('settings.role_all')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-delete-all', 'label' => trans('settings.role_all'), 'ariaLabel' => $deleteAllAria])
    </div>
</div>
