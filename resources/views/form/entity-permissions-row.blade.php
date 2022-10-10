{{--
$role - The Role to display this row for.
$entityType - String identifier for type of entity having permissions applied.
$permission - The entity permission containing the permissions.
--}}

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
        // TODO
        $inheriting = ($role->id === 0);
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
            @include('form.custom-checkbox', [
                'name' =>  'permissions[' . $role->id . '][view]',
                'label' => trans('common.view'),
                'value' => 'true',
                'checked' => $permission->view,
                'disabled' => $inheriting
            ])
        </div>
        @if($entityType !== 'page')
            <div class="px-l">
                @include('form.custom-checkbox', [
                    'name' =>  'permissions[' . $role->id . '][create]',
                    'label' => trans('common.create'),
                    'value' => 'true',
                    'checked' => $permission->create,
                    'disabled' => $inheriting
                ])
            </div>
        @endif
        <div class="px-l">
            @include('form.custom-checkbox', [
                'name' =>  'permissions[' . $role->id . '][update]',
                'label' => trans('common.update'),
                'value' => 'true',
                'checked' => $permission->update,
                'disabled' => $inheriting
            ])
        </div>
        <div class="px-l">
            @include('form.custom-checkbox', [
                'name' =>  'permissions[' . $role->id . '][delete]',
                'label' => trans('common.delete'),
                'value' => 'true',
                'checked' => $permission->delete,
                'disabled' => $inheriting
            ])
        </div>
    </div>
    @if($role->id !== 0)
        <div class="flex-container-row items-center px-m py-s">
            <button type="button"
                    class="text-neg p-m icon-button"
                    data-role-id="{{ $role->id }}"
                    data-role-name="{{ $role->display_name }}"
                    title="Remove Row">
                @icon('close') <span class="hide-over-m ml-xs">Remove Row</span>
            </button>
        </div>
    @endif
</div>