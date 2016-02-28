<?php namespace BookStack;


abstract class Entity extends Ownable
{

    /**
     * Compares this entity to another given entity.
     * Matches by comparing class and id.
     * @param $entity
     * @return bool
     */
    public function matches($entity)
    {
        return [get_class($this), $this->id] === [get_class($entity), $entity->id];
    }

    /**
     * Checks if an entity matches or contains another given entity.
     * @param Entity $entity
     * @return bool
     */
    public function matchesOrContains(Entity $entity)
    {
        $matches = [get_class($this), $this->id] === [get_class($entity), $entity->id];

        if ($matches) return true;

        if (($entity->isA('chapter') || $entity->isA('page')) && $this->isA('book')) {
            return $entity->book_id === $this->id;
        }

        if ($entity->isA('page') && $this->isA('chapter')) {
            return $entity->chapter_id === $this->id;
        }

        return false;
    }

    /**
     * Gets the activity objects for this entity.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany('BookStack\Activity', 'entity')->orderBy('created_at', 'desc');
    }

    /**
     * Get View objects for this entity.
     */
    public function views()
    {
        return $this->morphMany('BookStack\View', 'viewable');
    }

    /**
     * Get this entities restrictions.
     */
    public function restrictions()
    {
        return $this->morphMany('BookStack\Restriction', 'restrictable');
    }

    /**
     * Check if this entity has a specific restriction set against it.
     * @param $role_id
     * @param $action
     * @return bool
     */
    public function hasRestriction($role_id, $action)
    {
        return $this->restrictions->where('role_id', $role_id)->where('action', $action)->count() > 0;
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Cleaner method for is_a.
     * @param $type
     * @return bool
     */
    public static function isA($type)
    {
        return static::getClassName() === strtolower($type);
    }

    /**
     * Gets a limited-length version of the entities name.
     * @param int $length
     * @return string
     */
    public function getShortName($length = 25)
    {
        if(strlen($this->name) <= $length) return $this->name;
        return substr($this->name, 0, $length-3) . '...';
    }

    /**
     * Perform a full-text search on this entity.
     * @param string[] $fieldsToSearch
     * @param string[] $terms
     * @param string[] array $wheres
     * @return mixed
     */
    public static function fullTextSearchQuery($fieldsToSearch, $terms, $wheres = [])
    {
        $termString = '';
        foreach ($terms as $term) {
            $termString .= htmlentities($term) . '* ';
        }
        $fields = implode(',', $fieldsToSearch);
        $termStringEscaped = \DB::connection()->getPdo()->quote($termString);
        $search = static::addSelect(\DB::raw('*, MATCH(name) AGAINST('.$termStringEscaped.' IN BOOLEAN MODE) AS title_relevance'));
        $search = $search->whereRaw('MATCH(' . $fields . ') AGAINST(? IN BOOLEAN MODE)', [$termString]);

        // Add additional where terms
        foreach ($wheres as $whereTerm) {
            $search->where($whereTerm[0], $whereTerm[1], $whereTerm[2]);
        }

        // Load in relations
        if (static::isA('page'))  {
            $search = $search->with('book', 'chapter', 'createdBy', 'updatedBy');
        } else if (static::isA('chapter')) {
            $search = $search->with('book');
        }

        return $search->orderBy('title_relevance', 'desc');
    }

    /**
     * Get the url for this item.
     * @return string
     */
    abstract public function getUrl();

}
