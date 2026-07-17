<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Watch;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Builder;

class EntityWatchers
{
    /**
     * @var int[]
     */
    protected array $watchers = [];

    /**
     * @var int[]
     */
    protected array $ignorers = [];

    public function __construct(
        protected Entity $entity,
        protected int $watchLevel,
    ) {
        $this->build();
    }

    public function getWatcherUserIds(): array
    {
        return $this->watchers;
    }

    public function isUserIgnoring(int $userId): bool
    {
        return in_array($userId, $this->ignorers);
    }

    protected function build(): void
    {
        $watches = $this->getRelevantWatches();

        // Sort before de-duping, so that the order looped below follows book -> chapter -> page ordering
        usort($watches, function (Watch $watchA, Watch $watchB) {
            $entityTypeDiff = $watchA->watchable_type <=> $watchB->watchable_type;
            return $entityTypeDiff === 0 ? ($watchA->user_id <=> $watchB->user_id) : $entityTypeDiff;
        });

        // De-dupe by user id to get their most relevant level
        $levelByUserId = [];
        foreach ($watches as $watch) {
            $levelByUserId[$watch->user_id] = $watch->level;
        }

        // Populate the class arrays
        $this->watchers = array_keys(array_filter($levelByUserId, fn(int $level) => $level >= $this->watchLevel));
        $this->ignorers = array_keys(array_filter($levelByUserId, fn(int $level) => $level === 0));
    }

    /**
     * @return Watch[]
     */
    protected function getRelevantWatches(): array
    {
        /** @var Entity[] $entitiesInvolved */
        $entitiesInvolved = array_filter([
            $this->entity,
            $this->entity instanceof BookChild ? $this->entity->book : null,
            $this->entity instanceof Page ? $this->entity->chapter : null,
        ]);

        $query = Watch::query()->where(function (Builder $query) use ($entitiesInvolved) {
            foreach ($entitiesInvolved as $entity) {
                $query->orWhere(function (Builder $query) use ($entity) {
                    $query->where('watchable_type', '=', $entity->getMorphClass())
                        ->where('watchable_id', '=', $entity->id);
                });
            }
        });

        return $query->get([
            'level', 'watchable_id', 'watchable_type', 'user_id'
        ])->all();
    }
}
