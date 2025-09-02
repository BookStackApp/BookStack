<?php

namespace BookStack\Entities\Tools;

use BookStack\App\Model;
use BookStack\App\SluggableInterface;
use BookStack\Entities\Models\BookChild;
use Illuminate\Support\Str;

class SlugGenerator
{
    /**
     * Generate a fresh slug for the given entity.
     * The slug will be generated so that it doesn't conflict within the same parent item.
     */
    public function generate(SluggableInterface&Model $model, string $slugSource): string
    {
        $slug = $this->formatNameAsSlug($slugSource);
        while ($this->slugInUse($slug, $model)) {
            $slug .= '-' . Str::random(3);
        }

        return $slug;
    }

    /**
     * Format a name as a URL slug.
     */
    protected function formatNameAsSlug(string $name): string
    {
        $slug = Str::slug($name);
        if ($slug === '') {
            $slug = substr(md5(rand(1, 500)), 0, 5);
        }

        return $slug;
    }

    /**
     * Check if a slug is already in-use for this
     * type of model within the same parent.
     */
    protected function slugInUse(string $slug, SluggableInterface&Model $model): bool
    {
        $query = $model->newQuery()->where('slug', '=', $slug);

        if ($model instanceof BookChild) {
            $query->where('book_id', '=', $model->book_id);
        }

        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return $query->count() > 0;
    }
}
