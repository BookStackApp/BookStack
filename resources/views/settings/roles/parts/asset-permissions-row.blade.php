<div class="item-list-row flex-container-row items-center wrap">
    <div class="flex py-s px-m min-width-s">
        <strong>{{ $title }}</strong> <br>
        <a href="#" refs="permissions-table@toggle-row" class="text-small text-link">{{ trans('common.toggle_all') }}</a>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.create') }}<br></small>
        @if($permissionPrefix === 'page' || $permissionPrefix === 'chapter')
            @php $createOwnAria = trans('settings.role_permission_aria', ['action' => trans('common.create'), 'resource' => strtolower($title), 'value' => trans('settings.role_own')]); @endphp
            @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-create-own', 'label' => trans('settings.role_own'), 'ariaLabel' => $createOwnAria])
            <br>
        @endif
        @php $createAllAria = trans('settings.role_permission_aria', ['action' => trans('common.create'), 'resource' => strtolower($title), 'value' => trans('settings.role_all')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-create-all', 'label' => trans('settings.role_all'), 'ariaLabel' => $createAllAria])
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.view') }}<br></small>
        @php $viewOwnAria = trans('settings.role_permission_aria', ['action' => trans('common.view'), 'resource' => strtolower($title), 'value' => trans('settings.role_own')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-view-own', 'label' => trans('settings.role_own'), 'ariaLabel' => $viewOwnAria])
        <br>
        @php $viewAllAria = trans('settings.role_permission_aria', ['action' => trans('common.view'), 'resource' => strtolower($title), 'value' => trans('settings.role_all')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-view-all', 'label' => trans('settings.role_all'), 'ariaLabel' => $viewAllAria])
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.edit') }}<br></small>
        @php $editOwnAria = trans('settings.role_permission_aria', ['action' => trans('common.edit'), 'resource' => strtolower($title), 'value' => trans('settings.role_own')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-update-own', 'label' => trans('settings.role_own'), 'ariaLabel' => $editOwnAria])
        <br>
        @php $editAllAria = trans('settings.role_permission_aria', ['action' => trans('common.edit'), 'resource' => strtolower($title), 'value' => trans('settings.role_all')]); @endphp
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-update-all', 'label' => trans('settings.role_all'), 'ariaLabel' => $editAllAria])
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
