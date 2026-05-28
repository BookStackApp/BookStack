<div class="item-list-row flex-container-row items-center wrap">
    <div class="flex py-s px-m min-width-s">
        <strong>{{ $title }}</strong> <br>
        <a href="#" refs="permissions-table@toggle-row" class="text-small text-link">{{ trans('common.toggle_all') }}</a>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.create') }}<br></small>
        <strong class="text-muted opacity-70 text-large">-</strong>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.view') }}<br></small>
        @include('settings.roles.parts.checkbox', ['permission' => $permissionPrefix . '-view-all', 'label' => trans('settings.role_all')])
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.edit') }}<br></small>
        <strong class="text-muted opacity-70 text-large">-</strong>
    </div>
    <div class="flex py-s px-m min-width-xxs">
        <small class="hide-over-m bold">{{ trans('common.delete') }}<br></small>
        <small>{{ trans('settings.role_controlled_by_page_delete') }}</small>
    </div>
</div>
