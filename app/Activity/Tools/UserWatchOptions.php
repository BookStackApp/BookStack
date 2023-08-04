<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Watch;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Entity;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserWatchOptions
{
    public function __construct(
        protected User $user,
    ) {
    }

    public function canWatch(): bool
    {
        return $this->user->can('receive-notifications') && !$this->user->isDefault();
    }

    public function getEntityWatchLevel(Entity $entity): string
    {
        $levelValue = $this->entityQuery($entity)->first(['level'])->level ?? -1;
        return WatchLevels::levelValueToName($levelValue);
    }

    public function isWatching(Entity $entity): bool
    {
        return $this->entityQuery($entity)->exists();
    }

    public function updateEntityWatchLevel(Entity $entity, string $level): void
    {
        $levelValue = WatchLevels::levelNameToValue($level);
        if ($levelValue < 0) {
            $this->removeForEntity($entity);
            return;
        }

        $this->updateForEntity($entity, $levelValue);
    }

    protected function updateForEntity(Entity $entity, int $levelValue): void
    {
        Watch::query()->updateOrCreate([
            'watchable_id' => $entity->id,
            'watchable_type' => $entity->getMorphClass(),
            'user_id' => $this->user->id,
        ], [
            'level' => $levelValue,
        ]);
    }

    protected function removeForEntity(Entity $entity): void
    {
        $this->entityQuery($entity)->delete();
    }

    protected function entityQuery(Entity $entity): Builder
    {
        return Watch::query()->where('watchable_id', '=', $entity->id)
            ->where('watchable_type', '=', $entity->getMorphClass())
            ->where('user_id', '=', $this->user->id);
    }
}
