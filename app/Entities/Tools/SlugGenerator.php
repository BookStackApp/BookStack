<?php namespace BookStack\Entities;

use Illuminate\Support\Str;

class SlugGenerator
{

    /**
     * Generate a fresh slug for the given entity.
     * The slug will generated so it does not conflict within the same parent item.
     */
    public function generate(Entity $entity): string
    {
        $slug = $this->formatNameAsSlug($entity->name);
        while ($this->slugInUse($slug, $entity)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

    /**
     * Format a name as a url slug.
     */
    protected function formatNameAsSlug(string $name): string
    {
        $slug = Str::slug($name);
        if ($slug === "") {
            $slug = substr(md5(rand(1, 500)), 0, 5);
        }
        return $slug;
    }

    /**
     * Check if a slug is already in-use for this
     * type of model within the same parent.
     */
    protected function slugInUse(string $slug, Entity $entity): bool
    {
        $query = $entity->newQuery()->where('slug', '=', $slug);

        if ($entity instanceof BookChild) {
            $query->where('book_id', '=', $entity->book_id);
        }

        if ($entity->id) {
            $query->where('id', '!=', $entity->id);
        }

        return $query->count() > 0;
    }
}
