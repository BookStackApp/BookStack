<div class="content-permissions-row flex-container-row justify-space-between wrap">
    <div class="content-permissions-row-label gap-x-m flex-container-row items-center px-l py-m flex">
        <div class="text-large" title="{{ trans('common.role') }}">
            @icon('role')
        </div>
        <span>{{ $role->display_name }}</span>
        <button type="button"
                class="ml-auto flex-none text-small text-primary text-button hover-underline content-permissions-row-toggle-all hide-under-s"
                permissions-table-toggle-all-in-row
                >{{ trans('common.toggle_all') }}</button>
    </div>
    <div class="flex-container-row justify-space-between gap-x-xl wrap items-center">
        <div class="px-l">
            @include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.view'), 'action' => 'view'])
        </div>
        <div class="px-l">
            @if(!$model->isA('page'))
                @include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.create'), 'action' => 'create'])
            @endif
        </div>
        <div class="px-l">
            @include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.update'), 'action' => 'update'])
        </div>
        <div class="px-l">
            @include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.delete'), 'action' => 'delete'])
        </div>
    </div>
</div>