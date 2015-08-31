<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    /**
     * Relation for the user that created this entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('Oxbow\User', 'created_by');
    }

    /**
     * Relation for the user that updated this entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo('Oxbow\User', 'updated_by');
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
     * Gets the activity for this entity.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany('Oxbow\Activity', 'entity')->orderBy('created_at', 'desc');
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Cleaner method for is_a.
     * @param $type
     * @return bool
     */
    public function isA($type)
    {
        return $this->getName() === strtolower($type);
    }

    /**
     * Gets the class name.
     * @return string
     */
    public function getName()
    {
        $fullClassName = get_class($this);
        return strtolower(array_slice(explode('\\', $fullClassName), -1, 1)[0]);
    }

    /**
     * Perform a full-text search on this entity.
     * @param string[] $fieldsToSearch
     * @param string[] $terms
     * @return mixed
     */
    public static function fullTextSearch($fieldsToSearch, $terms)
    {
        $termString = '';
        foreach($terms as $term) {
            $termString .= $term . '* ';
        }
        $fields = implode(',', $fieldsToSearch);
        return static::whereRaw('MATCH(' . $fields . ') AGAINST(? IN BOOLEAN MODE)', [$termString])->get();
    }

}
