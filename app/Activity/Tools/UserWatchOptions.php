<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Watch;
use BookStack\Entities\Models\Entity;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserWatchOptions
{
    protected static array $levelByName = [
        'default' => -1,
        'ignore' => 0,
        'new' => 1,
        'updates' => 2,
        'comments' => 3,
    ];

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
        return $this->levelValueToName($levelValue);
    }

    public function isWatching(Entity $entity): bool
    {
        return $this->entityQuery($entity)->exists();
    }

    public function updateEntityWatchLevel(Entity $entity, string $level): void
    {
        $levelValue = $this->levelNameToValue($level);
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

    /**
     * @return string[]
     */
    public static function getAvailableLevelNames(): array
    {
        return array_keys(static::$levelByName);
    }

    protected static function levelNameToValue(string $level): int
    {
        return static::$levelByName[$level] ?? -1;
    }

    protected static function levelValueToName(int $level): string
    {
        foreach (static::$levelByName as $name => $value) {
            if ($level === $value) {
                return $name;
            }
        }

        return 'default';
    }
}
