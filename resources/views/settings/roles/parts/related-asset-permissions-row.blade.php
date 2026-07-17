<div class="item-list-row flex-container-row items-center wrap">
    <div class="flex py-s px-m min-width-s">
        <strong>{{ $title }}</strong> <br>
        <a href="#" refs="permissions-table@toggle-row" class="text-small text-link">{{ trans('common.toggle_all') }}</a>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.create') }}<br></small>
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-create-all', 'label' => ''])
        @if($permissionPrefix === 'comment')<sup class="text-muted">2</sup>@endif
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.view') }}<br></small>
        <small class="faded">{{ trans('settings.role_controlled_by_asset') }}@if($permissionPrefix === 'image')<sup class="text-muted">1</sup>@endif</small>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.edit') }}<br></small>
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-update-own', 'label' => trans('settings.role_own')])
        @if($permissionPrefix === 'comment')<sup class="text-muted">2</sup>@endif
        <br>
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-update-all', 'label' => trans('settings.role_all')])
        @if($permissionPrefix === 'comment')<sup class="text-muted">2</sup>@endif
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.delete') }}<br></small>
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-delete-own', 'label' => trans('settings.role_own')])
        <br>
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-delete-all', 'label' => trans('settings.role_all')])
    </div>
</div>