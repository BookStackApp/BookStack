<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

abstract class Entity extends Model
{

    /**
     * Relation for the user that created this entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('BookStack\User', 'created_by');
    }

    /**
     * Relation for the user that updated this entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo('BookStack\User', 'updated_by');
    }

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
     * Gets the activity objects for this entity.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany('BookStack\Activity', 'entity')->orderBy('created_at', 'desc');
    }

    /**
     * Get View objects for this entity.
     * @return mixed
     */
    public function views()
    {
        return $this->morphMany('BookStack\View', 'viewable');
    }

    /**
     * Get just the views for the current user.
     * @return mixed
     */
    public function userViews()
    {
        return $this->views()->where('user_id', '=', auth()->user()->id);
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Cleaner method for is_a.
     * @param $type
     * @return bool
     */
    public static function isA($type)
    {
        return static::getName() === strtolower($type);
    }

    /**
     * Gets the class name.
     * @return string
     */
    public static function getName()
    {
        return strtolower(array_slice(explode('\\', static::class), -1, 1)[0]);
    }

    /**
     * Perform a full-text search on this entity.
     * @param string[] $fieldsToSearch
     * @param string[] $terms
     * @param string[] array $wheres
     * @return mixed
     */
    public static function fullTextSearch($fieldsToSearch, $terms, $wheres = [])
    {
        $termString = '';
        foreach ($terms as $term) {
            $termString .= $term . '* ';
        }
        $fields = implode(',', $fieldsToSearch);
        $search = static::whereRaw('MATCH(' . $fields . ') AGAINST(? IN BOOLEAN MODE)', [$termString]);
        foreach ($wheres as $whereTerm) {
            $search->where($whereTerm[0], $whereTerm[1], $whereTerm[2]);
        }

        if (!static::isA('book')) {
            $search = $search->with('book');
        }

        if(static::isA('page')) {
            $search = $search->with('chapter');
        }

        return $search->get();
    }

    /**
     * Get the url for this item.
     * @return string
     */
    abstract public function getUrl();

}
