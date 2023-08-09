<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Watch;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserEntityWatchOptions
{
    protected ?array $watchMap = null;

    public function __construct(
        protected User $user,
        protected Entity $entity,
    ) {
    }

    public function canWatch(): bool
    {
        return $this->user->can('receive-notifications') && !$this->user->isDefault();
    }

    public function getWatchLevel(): string
    {
        return WatchLevels::levelValueToName($this->getWatchLevelValue());
    }

    public function isWatching(): bool
    {
        return $this->getWatchLevelValue() !== WatchLevels::DEFAULT;
    }

    public function getWatchedParent(): ?WatchedParentDetails
    {
        $watchMap = $this->getWatchMap();
        unset($watchMap[$this->entity->getMorphClass()]);

        if (isset($watchMap['chapter'])) {
            return new WatchedParentDetails('chapter', $watchMap['chapter']);
        }

        if (isset($watchMap['book'])) {
            return new WatchedParentDetails('book', $watchMap['book']);
        }

        return null;
    }

    public function updateWatchLevel(string $level): void
    {
        $levelValue = WatchLevels::levelNameToValue($level);
        if ($levelValue < 0) {
            $this->remove();
            return;
        }

        $this->updateLevel($levelValue);
    }

    public function getWatchMap(): array
    {
        if (!is_null($this->watchMap)) {
            return $this->watchMap;
        }

        $entities = [$this->entity];
        if ($this->entity instanceof BookChild) {
            $entities[] = $this->entity->book;
        }
        if ($this->entity instanceof Page && $this->entity->chapter) {
            $entities[] = $this->entity->chapter;
        }

        $query = Watch::query()->where(function (Builder $subQuery) use ($entities) {
            foreach ($entities as $entity) {
                $subQuery->orWhere(function (Builder $whereQuery) use ($entity) {
                    $whereQuery->where('watchable_type', '=', $entity->getMorphClass())
                        ->where('watchable_id', '=', $entity->id);
                });
            }
        });

        $this->watchMap = $query->get(['watchable_type', 'level'])
            ->pluck('level', 'watchable_type')
            ->toArray();

        return $this->watchMap;
    }

    protected function getWatchLevelValue()
    {
        return $this->getWatchMap()[$this->entity->getMorphClass()] ?? WatchLevels::DEFAULT;
    }

    protected function updateLevel(int $levelValue): void
    {
        Watch::query()->updateOrCreate([
            'watchable_id' => $this->entity->id,
            'watchable_type' => $this->entity->getMorphClass(),
            'user_id' => $this->user->id,
        ], [
            'level' => $levelValue,
        ]);
        $this->watchMap = null;
    }

    protected function remove(): void
    {
        $this->entityQuery()->delete();
        $this->watchMap = null;
    }

    protected function entityQuery(): Builder
    {
        return Watch::query()->where('watchable_id', '=', $this->entity->id)
            ->where('watchable_type', '=', $this->entity->getMorphClass())
            ->where('user_id', '=', $this->user->id);
    }
}
