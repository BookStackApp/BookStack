{{--
$modelType - The type of permission model; String matching one of: user, role, fallback
$modelId - The ID of the permission model.
$modelName - The name of the permission model.
$modelDescription - The description of the permission model.
$entityType - String identifier for type of entity having permissions applied.
$permission - The entity permission containing the permissions.
$inheriting - Boolean if the current row should be marked as inheriting default permissions. Used for "Everyone Else" role.
--}}

<div component="permissions-table" class="item-list-row flex-container-row justify-space-between wrap">
    <div class="gap-x-m flex-container-row items-center px-l py-m flex">
        <div class="text-large" title="{{  $modelType === 'fallback' ? trans('entities.permissions_role_everyone_else') : trans('common.role') }}">
            @icon($modelType === 'fallback' ? 'groups' : ($modelType === 'role' ? 'role' : 'user'))
        </div>
        <span>
            <strong>{{ $modelName }}</strong> <br>
            <small class="text-muted">{{ $modelDescription }}</small>
        </span>
        @if($modelType !== 'fallback')
            <button type="button"
                class="ml-auto flex-none text-small text-primary text-button hover-underline item-list-row-toggle-all hide-under-s"
                refs="permissions-table@toggle-all"
                ><strong>{{ trans('common.toggle_all') }}</strong></button>
        @endif
    </div>
    @if($modelType === 'fallback')
        <div class="px-l flex-container-row items-center" refs="entity-permissions@everyone-inherit">
            @include('form.custom-checkbox', [
                'name' => 'entity-permissions-inherit',
                'label' => trans('entities.permissions_inherit_defaults'),
                'value' => 'true',
                'checked' => $inheriting
            ])
        </div>
    @endif
    <div class="flex-container-row justify-space-between gap-x-xl wrap items-center">
        <input type="hidden" name="permissions[{{ $modelType }}][{{ $modelId }}][active]"
               @if($inheriting) disabled="disabled" @endif
               value="true">
        <div class="px-l">
            @include('form.custom-checkbox', [
                'name' =>  'permissions[' . $modelType . '][' . $modelId . '][view]',
                'label' => trans('common.view'),
                'value' => 'true',
                'checked' => $permission->view,
                'disabled' => $inheriting
            ])
        </div>
        @if($entityType !== 'page')
            <div class="px-l">
                @include('form.custom-checkbox', [
                    'name' =>  'permissions[' . $modelType . '][' . $modelId . '][create]',
                    'label' => trans('common.create'),
                    'value' => 'true',
                    'checked' => $permission->create,
                    'disabled' => $inheriting
                ])
            </div>
        @endif
        <div class="px-l">
            @include('form.custom-checkbox', [
                'name' =>  'permissions[' . $modelType . '][' . $modelId . '][update]',
                'label' => trans('common.update'),
                'value' => 'true',
                'checked' => $permission->update,
                'disabled' => $inheriting
            ])
        </div>
        <div class="px-l">
            @include('form.custom-checkbox', [
                'name' =>  'permissions[' . $modelType . '][' . $modelId . '][delete]',
                'label' => trans('common.delete'),
                'value' => 'true',
                'checked' => $permission->delete,
                'disabled' => $inheriting
            ])
        </div>
    </div>
    @if($modelType !== 'fallback')
        <div class="flex-container-row items-center px-m py-s">
            <button type="button"
                    class="text-neg p-m icon-button"
                    data-model-type="{{ $modelType }}"
                    data-model-id="{{ $modelId }}"
                    data-model-name="{{ $modelName }}"
                    title="{{ trans('common.remove') }}">
                @icon('close') <span class="hide-over-m ml-xs">{{ trans('common.remove') }}</span>
            </button>
        </div>
    @endif
</div>