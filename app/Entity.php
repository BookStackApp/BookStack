<?php namespace BookStack;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class Entity extends Ownable
{

    public $textField = 'description';

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

        if ($matches) {
            return true;
        }

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
        return $this->morphMany(Activity::class, 'entity')->orderBy('created_at', 'desc');
    }

    /**
     * Get View objects for this entity.
     */
    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Get the Tag models that have been user assigned to this entity.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tags()
    {
        return $this->morphMany(Tag::class, 'entity')->orderBy('order', 'asc');
    }

    /**
     * Get the comments for an entity
     * @param bool $orderByCreated
     * @return MorphMany
     */
    public function comments($orderByCreated = true)
    {
        $query = $this->morphMany(Comment::class, 'entity');
        return $orderByCreated ? $query->orderBy('created_at', 'asc') : $query;
    }

    /**
     * Get the related search terms.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function searchTerms()
    {
        return $this->morphMany(SearchTerm::class, 'entity');
    }

    /**
     * Get this entities restrictions.
     */
    public function permissions()
    {
        return $this->morphMany(EntityPermission::class, 'restrictable');
    }

    /**
     * Check if this entity has a specific restriction set against it.
     * @param $role_id
     * @param $action
     * @return bool
     */
    public function hasRestriction($role_id, $action)
    {
        return $this->permissions()->where('role_id', '=', $role_id)
            ->where('action', '=', $action)->count() > 0;
    }

    /**
     * Get the entity jointPermissions this is connected to.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function jointPermissions()
    {
        return $this->morphMany(JointPermission::class, 'entity');
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Cleaner method for is_a.
     * @param $type
     * @return bool
     */
    public static function isA($type)
    {
        return static::getType() === strtolower($type);
    }

    /**
     * Get entity type.
     * @return mixed
     */
    public static function getType()
    {
        return strtolower(static::getClassName());
    }

    /**
     * Get an instance of an entity of the given type.
     * @param $type
     * @return Entity
     */
    public static function getEntityInstance($type)
    {
        $types = ['Page', 'Book', 'Chapter'];
        $className = str_replace([' ', '-', '_'], '', ucwords($type));
        if (!in_array($className, $types)) {
            return null;
        }

        return app('BookStack\\' . $className);
    }

    /**
     * Gets a limited-length version of the entities name.
     * @param int $length
     * @return string
     */
    public function getShortName($length = 25)
    {
        if (strlen($this->name) <= $length) {
            return $this->name;
        }
        return substr($this->name, 0, $length - 3) . '...';
    }

    /**
     * Get the body text of this entity.
     * @return mixed
     */
    public function getText()
    {
        return $this->{$this->textField};
    }

    /**
     * Return a generalised, common raw query that can be 'unioned' across entities.
     * @return string
     */
    public function entityRawQuery()
    {
        return '';
    }

    /**
     * Get the url of this entity
     * @param $path
     * @return string
     */
    public function getUrl($path)
    {
        return '/';
    }
}
