<div class="setting-list">

    <div class="grid half">
        <div>
            <label class="setting-list-label">{{ trans('settings.role_details') }}</label>
        </div>
        <div>
            <div class="form-group">
                <label for="display_name">{{ trans('settings.role_name') }}</label>
                @include('form.text', ['name' => 'display_name', 'model' => $role])
            </div>
            <div class="form-group">
                <label for="description">{{ trans('settings.role_desc') }}</label>
                @include('form.text', ['name' => 'description', 'model' => $role])
            </div>
            <div class="form-group">
                @include('form.checkbox', ['name' => 'mfa_enforced', 'label' => trans('settings.role_mfa_enforced'), 'model' => $role ])
            </div>

            @if(in_array(config('auth.method'), ['ldap', 'saml2', 'oidc']))
                <div class="form-group">
                    <label for="name">{{ trans('settings.role_external_auth_id') }}</label>
                    @include('form.text', ['name' => 'external_auth_id', 'model' => $role])
                </div>
            @endif
        </div>
    </div>

    <div component="permissions-table">
        <label class="setting-list-label">{{ trans('settings.role_system') }}</label>
        <a href="#" refs="permissions-table@toggle-all" class="text-small text-link">{{ trans('common.toggle_all') }}</a>

        <div class="toggle-switch-list grid half mt-m">
            <div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'restrictions-manage-all', 'label' => trans('settings.role_manage_entity_permissions')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'restrictions-manage-own', 'label' => trans('settings.role_manage_own_entity_permissions')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'templates-manage', 'label' => trans('settings.role_manage_page_templates')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'access-api', 'label' => trans('settings.role_access_api')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'content-export', 'label' => trans('settings.role_export_content')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'editor-change', 'label' => trans('settings.role_editor_change')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'receive-notifications', 'label' => trans('settings.role_notifications')])</div>
            </div>
            <div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'settings-manage', 'label' => trans('settings.role_manage_settings')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'users-manage', 'label' => trans('settings.role_manage_users')])</div>
                <div>@include('settings.roles.parts.checkbox', ['permission' => 'user-roles-manage', 'label' => trans('settings.role_manage_roles')])</div>
                <p class="text-warn text-small mt-s mb-none">{{ trans('settings.roles_system_warning') }}</p>
            </div>
        </div>
    </div>

    <div>
        <label class="setting-list-label">{{ trans('settings.role_asset') }}</label>
        <p>{{ trans('settings.role_asset_desc') }}</p>

        @if (isset($role) && $role->system_name === 'admin')
            <p class="text-warn">{{ trans('settings.role_asset_admins') }}</p>
        @endif

        <div component="permissions-table"
             option:permissions-table:cell-selector=".item-list-row > div"
             option:permissions-table:row-selector=".item-list-row"
             class="item-list toggle-switch-list">
            <div class="item-list-row flex-container-row items-center hide-under-m bold">
                <div class="flex py-s px-m min-width-s">
                    <a href="#" refs="permissions-table@toggle-all" class="text-small text-link">{{ trans('common.toggle_all') }}</a>
                </div>
                <div refs="permissions-table@toggle-column" class="flex py-s px-m min-width-xxs">{{ trans('common.create') }}</div>
                <div refs="permissions-table@toggle-column" class="flex py-s px-m min-width-xxs">{{ trans('common.view') }}</div>
                <div refs="permissions-table@toggle-column" class="flex py-s px-m min-width-xxs">{{ trans('common.edit') }}</div>
                <div refs="permissions-table@toggle-column" class="flex py-s px-m min-width-xxs">{{ trans('common.delete') }}</div>
            </div>
            @include('settings.roles.parts.asset-permissions-row', ['title' => trans('entities.shelves'), 'permissionPrefix' => 'bookshelf'])
            @include('settings.roles.parts.asset-permissions-row', ['title' => trans('entities.books'), 'permissionPrefix' => 'book'])
            @include('settings.roles.parts.asset-permissions-row', ['title' => trans('entities.chapters'), 'permissionPrefix' => 'chapter'])
            @include('settings.roles.parts.asset-permissions-row', ['title' => trans('entities.pages'), 'permissionPrefix' => 'page'])
            @include('settings.roles.parts.related-asset-permissions-row', ['title' => trans('entities.images'), 'permissionPrefix' => 'image', 'refMark' => '1'])
            @include('settings.roles.parts.related-asset-permissions-row', ['title' => trans('entities.attachments'), 'permissionPrefix' => 'attachment'])
            @include('settings.roles.parts.related-asset-permissions-row', ['title' => trans('entities.comments'), 'permissionPrefix' => 'comment'])
        </div>

        <div>
            <p class="text-muted text-small p-m">
                <sup>1</sup> {{ trans('settings.role_asset_image_view_note') }}
            </p>
        </div>
    </div>
</div>