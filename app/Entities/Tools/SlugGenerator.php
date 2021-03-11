<?php namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\BookChild;
use BookStack\Interfaces\Sluggable;
use Illuminate\Support\Str;

class SlugGenerator
{

    /**
     * Generate a fresh slug for the given entity.
     * The slug will generated so it does not conflict within the same parent item.
     */
    public function generate(Sluggable $model): string
    {
        $slug = $this->formatNameAsSlug($model->name);
        while ($this->slugInUse($slug, $model)) {
            $slug .= '-' . Str::random(3);
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
    protected function slugInUse(string $slug, Sluggable $model): bool
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
