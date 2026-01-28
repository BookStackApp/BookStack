<?php

namespace BookStack\Entities;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\EntityShareLink;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use BookStack\Permissions\Permission;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Database\Eloquent\Collection;

class EntityShareLinkService
{
    public function __construct(
        protected PermissionApplicator $permissions,
        protected EntityQueries $queries
    ) {
    }

    /**
     * Create a new share link for the given entity.
     */
    public function createShareLink(Entity $entity, ?string $name = null): EntityShareLink
    {
        $shareLink = new EntityShareLink([
            'entity_id' => $entity->id,
            'entity_type' => $entity->getMorphClass(),
            'token' => EntityShareLink::generateToken(),
            'name' => $name,
            'created_by' => user()->id,
        ]);

        $shareLink->save();

        Activity::add(ActivityType::SHARE_LINK_CREATE, $entity);

        return $shareLink;
    }

    /**
     * Delete a share link.
     */
    public function deleteShareLink(EntityShareLink $shareLink): void
    {
        $currentUser = user();
        $shareLink->load('entity');

        if ($shareLink->created_by === $currentUser->id) {
            $entity = $shareLink->entity;
            $shareLink->delete();

            if ($entity) {
                Activity::add(ActivityType::SHARE_LINK_DELETE, $entity);
            }

            return;
        }

        if (userCan(Permission::SettingsManage)) {
            $entity = $shareLink->entity;
            $shareLink->delete();

            if ($entity) {
                Activity::add(ActivityType::SHARE_LINK_DELETE, $entity);
            }

            return;
        }

        if (userCan(Permission::ContentShareManage) && $shareLink->entity) {
            try {
                $entity = $this->queries->findVisibleById($shareLink->entity->getType(), $shareLink->entity->id);
                if ($entity) {
                    $shareLink->delete();
                    Activity::add(ActivityType::SHARE_LINK_DELETE, $entity);
                    return;
                }
            } catch (NotFoundException $e) {
            }
        }

        throw new PermissionsException(trans('errors.permission'));
    }

    /**
     * Get all share links for a given entity.
     */
    public function getShareLinksForEntity(Entity $entity): Collection
    {
        return EntityShareLink::query()
            ->forEntity($entity)
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all share links in the system (for admin overview).
     */
    public function getAllShareLinks(): Collection
    {
        return EntityShareLink::query()
            ->with(['createdBy', 'entity'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get share links created by a specific user.
     */
    public function getShareLinksForUser(User $user): Collection
    {
        return EntityShareLink::query()
            ->where('created_by', '=', $user->id)
            ->with('entity')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Validate a share token and return the associated entity.
     */
    public function validateAndGetEntity(string $token): ?Entity
    {
        $shareLink = EntityShareLink::query()
            ->where('token', '=', $token)
            ->with('entity')
            ->first();

        if (!$shareLink || !$shareLink->entity) {
            return null;
        }

        return $shareLink->entity;
    }

    /**
     * Find a share link by token.
     */
    public function findByToken(string $token): ?EntityShareLink
    {
        return EntityShareLink::query()
            ->where('token', '=', $token)
            ->with('entity')
            ->first();
    }

    /**
     * Check if an entity has any active share links.
     */
    public function entityHasShareLinks(Entity $entity): bool
    {
        return EntityShareLink::query()
            ->forEntity($entity)
            ->exists();
    }

    /**
     * Get the count of share links for an entity.
     */
    public function getShareLinkCount(Entity $entity): int
    {
        return EntityShareLink::query()
            ->forEntity($entity)
            ->count();
    }
}
