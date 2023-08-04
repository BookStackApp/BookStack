<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Watch;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Builder;

class EntityWatchers
{
    protected array $watchers = [];
    protected array $ignorers = [];

    public function __construct(
        protected Entity $entity,
        protected int $watchLevel,
    ) {
        $this->build();
    }

    protected function build(): void
    {
        $watches = $this->getRelevantWatches();

        // TODO - De-dupe down watches per-user across entity types
        // so we end up with [user_id => status] values
        // then filter to current watch level, considering ignores,
        // then populate the class watchers/ignores with ids.
    }

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
