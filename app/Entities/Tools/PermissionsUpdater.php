<?php

namespace BookStack\Entities\Tools;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Permissions\EntityPermission;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Entity;
use BookStack\Facades\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PermissionsUpdater
{
    /**
     * Update an entities permissions from a permission form submit request.
     */
    public function updateFromPermissionsForm(Entity $entity, Request $request)
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
     * Update the owner of the given entity.
     * Checks the user exists in the system first.
     * Does not save the model, just updates it.
     */
    protected function updateOwnerFromId(Entity $entity, int $newOwnerId)
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

        return $formatted;
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
