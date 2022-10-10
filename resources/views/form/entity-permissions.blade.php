<form component="entity-permissions"
      option:entity-permissions:entity-type="{{ $model->getType() }}"
      action="{{ $model->getUrl('/permissions') }}"
      method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">

    <div class="grid half left-focus v-center">
        <div>
            <p class="mb-none mt-m">{{ trans('entities.permissions_intro') }}</p>
        </div>
        <div>
            <div class="form-group">
                <label for="owner">{{ trans('entities.permissions_owner') }}</label>
                @include('form.user-select', ['user' => $model->ownedBy, 'name' => 'owned_by'])
            </div>
        </div>
    </div>

    @if($model instanceof \BookStack\Entities\Models\Bookshelf)
        <p class="text-warn">{{ trans('entities.shelves_permissions_cascade_warning') }}</p>
    @endif

    <div refs="entity-permissions@role-container" class="content-permissions mt-m mb-m">
        @foreach($data->permissionsWithRoles() as $permission)
            @include('form.entity-permissions-row', [
                'permission' => $permission,
                'role' => $permission->role,
                'entityType' => $model->getType()
            ])
        @endforeach
    </div>

    <div class="flex-container-row justify-flex-end mb-xl">
        <div>
            <label for="role_select">Override permissions for role</label>
            <select name="role_select" id="role_select" refs="entity-permissions@role-select">
                <option value="">{{ trans('common.select') }}</option>
                @foreach($data->rolesNotAssigned() as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="content-permissions mt-m mb-xl">
        @include('form.entity-permissions-row', [
                'role' => $data->everyoneElseRole(),
                'permission' => new \BookStack\Auth\Permissions\EntityPermission(),
                'entityType' => $model->getType(),
            ])
    </div>

    <div class="text-right">
        <a href="{{ $model->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
        <button type="submit" class="button">{{ trans('entities.permissions_save') }}</button>
    </div>
</form>