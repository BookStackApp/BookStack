<?php
  /** @var \BookStack\Auth\Permissions\PermissionFormData $data */
?>
<form component="entity-permissions"
      option:entity-permissions:entity-type="{{ $model->getType() }}"
      action="{{ $model->getUrl('/permissions') }}"
      method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">

    <div class="grid half left-focus v-end gap-m wrap">
        <div>
            <h1 class="list-heading">{{ $title }}</h1>
            <p class="text-muted mb-s">
                {{ trans('entities.permissions_desc') }}

                @if($model instanceof \BookStack\Entities\Models\Book)
                    <br> {{ trans('entities.permissions_book_cascade') }}
                @elseif($model instanceof \BookStack\Entities\Models\Chapter)
                    <br> {{ trans('entities.permissions_chapter_cascade') }}
                @endif
            </p>

            @if($model instanceof \BookStack\Entities\Models\Bookshelf)
                <p class="text-warn">{{ trans('entities.shelves_permissions_cascade_warning') }}</p>
            @endif
        </div>
        <div class="flex-container-row justify-flex-end">
            <div class="form-group mb-m">
                <label for="owner">{{ trans('entities.permissions_owner') }}</label>
                @include('form.user-select', ['user' => $model->ownedBy, 'name' => 'owned_by'])
            </div>
        </div>
    </div>

    <hr>

    <div refs="entity-permissions@user-container" class="item-list mt-m mb-m">
        @foreach($data->permissionsWithUsers() as $permission)
            @include('form.entity-permissions-row', [
                'permission' => $permission,
                'modelType' => 'user',
                'modelId' => $permission->user->id,
                'modelName' => $permission->user->name,
                'modelDescription' => '',
                'entityType' => $model->getType(),
                'inheriting' => false,
            ])
        @endforeach
    </div>

    <div class="flex-container-row justify-flex-end mb-xl">
        <div refs="entity-permissions@user-select-container" class="flex-container-row items-center gap-m">
            <label for="user_select" class="m-none p-none"><span class="bold">{{ trans('entities.permissions_user_override') }}</span></label>
            @include('form.user-select', ['name' => 'user_select', 'user' => null])
        </div>
    </div>

    <div refs="entity-permissions@role-container" class="item-list mt-m mb-m">
        @foreach($data->permissionsWithRoles() as $permission)
            @include('form.entity-permissions-row', [
                'permission' => $permission,
                'modelType' => 'role',
                'modelId' => $permission->role->id,
                'modelName' => $permission->role->display_name,
                'modelDescription' => $permission->role->description,
                'entityType' => $model->getType(),
                'inheriting' => false,
            ])
        @endforeach
    </div>

    <div class="flex-container-row justify-flex-end mb-xl">
        <div class="flex-container-row items-center gap-m">
            <label for="role_select" class="m-none p-none"><span class="bold">{{ trans('entities.permissions_role_override') }}</span></label>
            <select name="role_select" id="role_select" refs="entity-permissions@role-select">
                <option value="">{{ trans('common.select') }}</option>
                @foreach($data->rolesNotAssigned() as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="item-list mt-m mb-xl">
        @include('form.entity-permissions-row', [
                'modelType' => 'fallback',
                'modelId' => 0,
                'modelName' => trans('entities.permissions_role_everyone_else'),
                'modelDescription' => trans('entities.permissions_role_everyone_else_desc'),
                'permission' => $data->everyoneElseEntityPermission(),
                'entityType' => $model->getType(),
                'inheriting' => $data->everyoneElseInheriting(),
            ])
    </div>

    <hr class="mb-m">

    <div class="text-right">
        <a href="{{ $model->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
        <button type="submit" class="button">{{ trans('entities.permissions_save') }}</button>
    </div>
</form>