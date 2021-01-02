<?php namespace BookStack\Entities\Tools;

use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
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
        $restricted = $request->get('restricted') === 'true';
        $permissions = $request->get('restrictions', null);
        $ownerId = $request->get('owned_by', null);

        $entity->restricted = $restricted;
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

        Activity::addForEntity($entity, ActivityType::PERMISSIONS_UPDATE);
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
     * Format permissions provided from a permission form to be
     * EntityPermission data.
     */
    protected function formatPermissionsFromRequestToEntityPermissions(array $permissions): Collection
    {
        return collect($permissions)->flatMap(function ($restrictions, $roleId) {
            return collect($restrictions)->keys()->map(function ($action) use ($roleId) {
                return [
                    'role_id' => $roleId,
                    'action' => strtolower($action),
                ] ;
            });
        });
    }
}
