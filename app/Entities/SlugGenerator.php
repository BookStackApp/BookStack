<?php namespace BookStack\Entities;

class SlugGenerator
{

    protected $entity;

    /**
     * SlugGenerator constructor.
     * @param $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Generate a fresh slug for the given entity.
     * The slug will generated so it does not conflict within the same parent item.
     */
    public function generate(): string
    {
        $slug = $this->formatNameAsSlug($this->entity->name);
        while ($this->slugInUse($slug)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

    /**
     * Format a name as a url slug.
     */
    protected function formatNameAsSlug(string $name): string
    {
        $slug = preg_replace('/[\+\/\\\?\@\}\{\.\,\=\[\]\#\&\!\*\'\;\:\$\%]/', '', mb_strtolower($name));
        $slug = preg_replace('/\s{2,}/', ' ', $slug);
        $slug = str_replace(' ', '-', $slug);
        if ($slug === "") {
            $slug = substr(md5(rand(1, 500)), 0, 5);
        }
        return $slug;
    }

    /**
     * Check if a slug is already in-use for this
     * type of model within the same parent.
     */
    protected function slugInUse(string $slug): bool
    {
        $query = $this->entity->newQuery()->where('slug', '=', $slug);

        if ($this->entity instanceof BookChild) {
            $query->where('book_id', '=', $this->entity->book_id);
        }

        if ($this->entity->id) {
            $query->where('id', '!=', $this->entity->id);
        }

        return $query->count() > 0;
    }
}
