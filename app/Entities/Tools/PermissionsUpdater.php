<?php

namespace BookStack\Entities\Tools;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Entity;
use BookStack\Facades\Activity;
use BookStack\Permissions\Models\EntityPermission;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Http\Request;

class PermissionsUpdater
{
    /**
     * Update an entities permissions from a permission form submit request.
     */
    public function updateFromPermissionsForm(Entity $entity, Request $request): void
    {
        $permissions = $request->get('permissions', null);
        $ownerId = $request->get('owned_by', null);

        $entity->permissions()->delete();

        if (!is_null($permissions)) {
            $entityPermissionData = $this->formatPermissionsFromRequestToEntityPermissions($permissions);
            $entity->permissions()->createMany($entityPermissionData);
        }

        if (!is_null($ownerId)) {
            $this->updateOwnerFromId($entity, intval($ownerId));
        }

        $entity->save();
        $entity->rebuildPermissions();

        Activity::add(ActivityType::PERMISSIONS_UPDATE, $entity);
    }

    /**
     * Update permissions from API request data.
     */
    public function updateFromApiRequestData(Entity $entity, array $data): void
    {
        if (isset($data['role_permissions'])) {
            $entity->permissions()->where('role_id', '!=', 0)->delete();
            $rolePermissionData = $this->formatPermissionsFromApiRequestToEntityPermissions($data['role_permissions'] ?? [], false);
            $entity->permissions()->createMany($rolePermissionData);
        }

        if (array_key_exists('fallback_permissions', $data)) {
            $entity->permissions()->where('role_id', '=', 0)->delete();
        }

        if (isset($data['fallback_permissions']['inheriting']) && $data['fallback_permissions']['inheriting'] !== true) {
            $fallbackData = $data['fallback_permissions'];
            $fallbackData['role_id'] = 0;
            $rolePermissionData = $this->formatPermissionsFromApiRequestToEntityPermissions([$fallbackData], true);
            $entity->permissions()->createMany($rolePermissionData);
        }

        if (isset($data['owner_id'])) {
            $this->updateOwnerFromId($entity, intval($data['owner_id']));
        }

        $entity->save();
        $entity->rebuildPermissions();

        Activity::add(ActivityType::PERMISSIONS_UPDATE, $entity);
    }

    /**
     * Update the owner of the given entity.
     * Checks the user exists in the system first.
     * Does not save the model, just updates it.
     */
    protected function updateOwnerFromId(Entity $entity, int $newOwnerId): void
    {
        $newOwner = User::query()->find($newOwnerId);
        if (!is_null($newOwner)) {
            $entity->owned_by = $newOwner->id;
        }
    }

    /**
     * Format permissions provided from a permission form to be EntityPermission data.
     */
    protected function formatPermissionsFromRequestToEntityPermissions(array $permissions): array
    {
        $formatted = [];

        foreach ($permissions as $roleId => $info) {
            $entityPermissionData = ['role_id' => $roleId];
            foreach (EntityPermission::PERMISSIONS as $permission) {
                $entityPermissionData[$permission] = (($info[$permission] ?? false) === "true");
            }
            $formatted[] = $entityPermissionData;
        }

        return $this->filterEntityPermissionDataUponRole($formatted, true);
    }

    protected function formatPermissionsFromApiRequestToEntityPermissions(array $permissions, bool $allowFallback): array
    {
        $formatted = [];

        foreach ($permissions as $requestPermissionData) {
            $entityPermissionData = ['role_id' => $requestPermissionData['role_id']];
            foreach (EntityPermission::PERMISSIONS as $permission) {
                $entityPermissionData[$permission] = boolval($requestPermissionData[$permission] ?? false);
            }
            $formatted[] = $entityPermissionData;
        }

        return $this->filterEntityPermissionDataUponRole($formatted, $allowFallback);
    }

    protected function filterEntityPermissionDataUponRole(array $entityPermissionData, bool $allowFallback): array
    {
        $roleIds = [];
        foreach ($entityPermissionData as $permissionEntry) {
            $roleIds[] = intval($permissionEntry['role_id']);
        }

        $actualRoleIds = array_unique(array_values(array_filter($roleIds)));
        $rolesById = Role::query()->whereIn('id', $actualRoleIds)->get('id')->keyBy('id');

        return array_values(array_filter($entityPermissionData, function ($data) use ($rolesById, $allowFallback) {
            if (intval($data['role_id']) === 0) {
                return $allowFallback;
            }

            return $rolesById->has($data['role_id']);
        }));
    }

    /**
     * Copy down the permissions of the given shelf to all child books.
     */
    public function updateBookPermissionsFromShelf(Bookshelf $shelf, $checkUserPermissions = true): int
    {
        $shelfPermissions = $shelf->permissions()->get(['role_id', 'view', 'create', 'update', 'delete'])->toArray();
        $shelfBooks = $shelf->books()->get(['id', 'owned_by']);
        $updatedBookCount = 0;

        /** @var Book $book */
        foreach ($shelfBooks as $book) {
            if ($checkUserPermissions && !userCan('restrictions-manage', $book)) {
                continue;
            }
            $book->permissions()->delete();
            $book->permissions()->createMany($shelfPermissions);
            $book->rebuildPermissions();
            $updatedBookCount++;
        }

        return $updatedBookCount;
    }
}
