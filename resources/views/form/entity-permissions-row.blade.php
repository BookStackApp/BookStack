<div component="permissions-table" class="content-permissions-row flex-container-row justify-space-between wrap">
    <div class="gap-x-m flex-container-row items-center px-l py-m flex">
        <div class="text-large" title="{{ $role->id === 0 ? 'Everyone Else' : trans('common.role') }}">
            @icon($role->id === 0 ? 'groups' : 'role')
        </div>
        <span>
            <strong>{{ $role->display_name }}</strong> <br>
            <small class="text-muted">{{ $role->description }}</small>
        </span>
        @if($role->id !== 0)
            <button type="button"
                class="ml-auto flex-none text-small text-primary text-button hover-underline content-permissions-row-toggle-all hide-under-s"
                refs="permissions-table@toggle-all"
                ><strong>{{ trans('common.toggle_all') }}</strong></button>
        @endif
    </div>
    @php
        $inheriting = ($role->id === 0 && !$model->restricted);
    @endphp
    @if($role->id === 0)
        <div class="px-l flex-container-row items-center" refs="entity-permissions@everyoneInherit">
            @include('form.custom-checkbox', [
                'name' => 'entity-permissions-inherit',
                'label' => 'Inherit defaults',
                'value' => 'true',
                'checked' => $inheriting
            ])
        </div>
    @endif
    <div class="flex-container-row justify-space-between gap-x-xl wrap items-center">
        <input type="hidden" name="permissions[{{ $role->id }}][active]" value="true">
        <div class="px-l">
            @include('form.restriction-checkbox', ['name'=>'permissions', 'label' => trans('common.view'), 'action' => 'view', 'disabled' => $inheriting])
        </div>
        <div class="px-l">
            @if(!$model instanceof \BookStack\Entities\Models\Page)
                @include('form.restriction-checkbox', ['name'=>'permissions', 'label' => trans('common.create'), 'action' => 'create', 'disabled' => $inheriting])
            @endif
        </div>
        <div class="px-l">
            @include('form.restriction-checkbox', ['name'=>'permissions', 'label' => trans('common.update'), 'action' => 'update', 'disabled' => $inheriting])
        </div>
        <div class="px-l">
            @include('form.restriction-checkbox', ['name'=>'permissions', 'label' => trans('common.delete'), 'action' => 'delete', 'disabled' => $inheriting])
        </div>
    </div>
</div>