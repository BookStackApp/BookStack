<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\EntityTable;
use BookStack\Entities\Models\SlugHistory as SlugHistoryModel;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Support\Facades\DB;

class SlugHistory
{
    public function __construct(
        protected PermissionApplicator $permissions,
    ) {
    }

    /**
     * Record the current slugs for the given entity.
     */
    public function recordForEntity(Entity $entity): void
    {
        if (!$entity->id || !$entity->slug) {
            return;
        }

        $parentSlug = null;
        if ($entity instanceof BookChild) {
            $parentSlug = $entity->book()->first()?->slug;
        }

        $latest = $this->getLatestEntryForEntity($entity);
        if ($latest && $latest->slug === $entity->slug && $latest->parent_slug === $parentSlug) {
            return;
        }

        $info = [
            'sluggable_type' => $entity->getMorphClass(),
            'sluggable_id'   => $entity->id,
            'slug'           => $entity->slug,
            'parent_slug'    => $parentSlug,
        ];

        $entry = new SlugHistoryModel();
        $entry->forceFill($info);
        $entry->save();

        if ($entity instanceof Book) {
            $this->recordForBookChildren($entity);
        }
    }

    protected function recordForBookChildren(Book $book): void
    {
        $query = EntityTable::query()
            ->select(['type', 'id', 'slug', DB::raw("'{$book->slug}' as parent_slug"), DB::raw('now() as created_at'), DB::raw('now() as updated_at')])
            ->where('book_id', '=', $book->id)
            ->whereNotNull('book_id');

        SlugHistoryModel::query()->insertUsing(
            ['sluggable_type', 'sluggable_id', 'slug', 'parent_slug', 'created_at', 'updated_at'],
            $query
        );
    }

    /**
     * Find the latest visible entry for an entity which uses the given slug(s) in the history.
     */
    public function lookupEntityIdUsingSlugs(string $type, string $slug, string $parentSlug = ''): ?int
    {
        $query = SlugHistoryModel::query()
            ->where('sluggable_type', '=', $type)
            ->where('slug', '=', $slug);

        if ($parentSlug) {
            $query->where('parent_slug', '=', $parentSlug);
        }

        $query = $this->permissions->restrictEntityRelationQuery($query, 'slug_history', 'sluggable_id', 'sluggable_type');

        /** @var SlugHistoryModel|null $result */
        $result = $query->orderBy('created_at', 'desc')->first();

        return $result?->sluggable_id;
    }

    protected function getLatestEntryForEntity(Entity $entity): SlugHistoryModel|null
    {
        return SlugHistoryModel::query()
            ->where('sluggable_type', '=', $entity->getMorphClass())
            ->where('sluggable_id', '=', $entity->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
