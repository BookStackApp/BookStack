<?php

namespace BookStack\Entities\Tools;

use BookStack\App\Model;
use BookStack\App\Sluggable;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\RecordChild;
use Illuminate\Support\Str;

class SlugGenerator
{
    /**
     * Generate a fresh slug for the given entity.
     * The slug will be generated so that it doesn't conflict within the same parent item.
     */
    public function generate(Sluggable $model): string
    {
        $slug = $this->formatNameAsSlug($model->name);
        // dd($model->toArray(), $slug);
        if (array_key_exists('record_id', $model->getAttributes())) {
            // dd('record_id is present');
            while ($this->recordSlugInUse($slug, $model)) {
                $slug .= '-' . Str::random(3);
            }
        }
        else{
            
            // dd($model->toArray(), $slug);
            while ($this->slugInUse($slug, $model)) {
                $slug .= '-' . Str::random(3);
            }
        }

        return $slug;
    }

    /**
     * Format a name as a url slug.
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
     *
     * @param Sluggable&Model $model
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

    /**
     * Check if a slug is already in-use for this
     * type of model within the same parent.
     *
     * @param Sluggable&Model $model
     */
    protected function recordSlugInUse(string $slug, Sluggable $model): bool
    {
        $query = $model->newQuery()->where('slug', '=', $slug);

        if ($model instanceof RecordChild) {
            $query->where('record_id', '=', $model->book_id);
        }

        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return $query->count() > 0;
    }
}
