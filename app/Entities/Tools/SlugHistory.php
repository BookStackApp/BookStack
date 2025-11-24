<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use Illuminate\Support\Facades\DB;

class SlugHistory
{
    /**
     * Record the current slugs for the given entity.
     */
    public function recordForEntity(Entity $entity): void
    {
        if (!$entity->id || !$entity->slug) {
            return;
        }

        $latest = $this->getLatestEntryForEntity($entity);
        if ($latest && $latest->slug === $entity->slug && $latest->parent_slug === $entity->getParent()?->slug) {
            return;
        }

        $parentSlug = null;
        if ($entity instanceof BookChild) {
            $parentSlug = $entity->book()->first()?->slug;
        }

        $info = [
            'sluggable_type' => $entity->getMorphClass(),
            'sluggable_id'   => $entity->id,
            'slug'           => $entity->slug,
            'parent_slug'    => $parentSlug,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        DB::table('slug_history')->insert($info);
    }

    protected function getLatestEntryForEntity(Entity $entity): \stdClass|null
    {
        return DB::table('slug_history')
            ->where('sluggable_type', '=', $entity->getMorphClass())
            ->where('sluggable_id', '=', $entity->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
