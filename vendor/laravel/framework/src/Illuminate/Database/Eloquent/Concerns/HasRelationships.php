<?php

namespace Illuminate\Database\Eloquent\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

trait HasRelationships
{
    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = [];

    /**
     * The many to many relationship methods.
     *
     * @var array
     */
    public static $manyMethods = [
        'belongsToMany', 'morphToMany', 'morphedByMany',
        'guessBelongsToManyRelation', 'findFirstMethodThatIsntRelation',
    ];

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new MorphOne($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsTo(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function morphTo($name = null, $type = null, $id = null)
    {
        // If no name is provided, we will use the backtrace to get the function name
        // since that is most likely the name of the polymorphic interface. We can
        // use that to get both the class and foreign key that will be utilized.
        $name = $name ?: $this->guessBelongsToRelation();

        list($type, $id) = $this->getMorphs(
            Str::snake($name), $type, $id
        );

        // If the type value is null it is probably safe to assume we're eager loading
        // the relationship. In this case we'll just pass in a dummy query where we
        // need to remove any eager loads that may already be defined on a model.
        return empty($class = $this->{$type})
                    ? $this->morphEagerTo($name, $type, $id)
                    : $this->morphInstanceTo($class, $name, $type, $id);
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    protected function morphEagerTo($name, $type, $id)
    {
        return new MorphTo(
            $this->newQuery()->setEagerLoads([]), $this, $id, null, $type, $name
        );
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $target
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    protected function morphInstanceTo($target, $name, $type, $id)
    {
        $instance = $this->newRelatedInstance(
            static::getActualClassNameForMorph($target)
        );

        return new MorphTo(
            $instance->newQuery(), $this, $id, $instance->getKeyName(), $type, $name
        );
    }

    /**
     * Retrieve the actual class name for a given morph class.
     *
     * @param  string  $class
     * @return string
     */
    public static function getActualClassNameForMorph($class)
    {
        return Arr::get(Relation::morphMap(), $class, $class);
    }

    /**
     * Guess the "belongs to" relationship name.
     *
     * @return string
     */
    protected function guessBelongsToRelation()
    {
        list($one, $two, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        return $caller['function'];
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    /**
     * Define a has-many-through relationship.
     *
     * @param  string  $related
     * @param  string  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null)
    {
        $through = new $through;

        $firstKey = $firstKey ?: $this->getForeignKey();

        $secondKey = $secondKey ?: $through->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        $instance = $this->newRelatedInstance($related);

        return new HasManyThrough($instance->newQuery(), $this, $through, $firstKey, $secondKey, $localKey);
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new MorphMany($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $relatedKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $relatedKey = null, $relation = null)
    {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $relatedKey = $relatedKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return new BelongsToMany(
            $instance->newQuery(), $this, $table, $foreignKey, $relatedKey, $relation
        );
    }

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $relatedKey
     * @param  bool  $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $relatedKey = null, $inverse = false)
    {
        $caller = $this->guessBelongsToManyRelation();

        // First, we will need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we will make the query
        // instances, as well as the relationship instances we need for these.
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $name.'_id';

        $relatedKey = $relatedKey ?: $instance->getForeignKey();

        // Now we're ready to create a new query builder for this related model and
        // the relationship instances for this relation. This relations will set
        // appropriate query constraints then entirely manages the hydrations.
        $table = $table ?: Str::plural($name);

        return new MorphToMany(
            $instance->newQuery(), $this, $name, $table,
            $foreignKey, $relatedKey, $caller, $inverse
        );
    }

    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $relatedKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphedByMany($related, $name, $table = null, $foreignKey = null, $relatedKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        // For the inverse of the polymorphic many-to-many relations, we will change
        // the way we determine the foreign and other keys, as it is the opposite
        // of the morph-to-many method since we're figuring out these inverses.
        $relatedKey = $relatedKey ?: $name.'_id';

        return $this->morphToMany($related, $name, $table, $foreignKey, $relatedKey, true);
    }

    /**
     * Get the relationship name of the belongs to many.
     *
     * @return string
     */
    protected function guessBelongsToManyRelation()
    {
        $caller = Arr::first(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), function ($trace) {
            return ! in_array($trace['function'], Model::$manyMethods);
        });

        return ! is_null($caller) ? $caller['function'] : null;
    }

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param  string  $related
     * @return string
     */
    public function joiningTable($related)
    {
        // The joining table name, by convention, is simply the snake cased models
        // sorted alphabetically and concatenated with an underscore, so we can
        // just sort the models and join them together to get the table name.
        $models = [
            Str::snake(class_basename($related)),
            Str::snake(class_basename($this)),
        ];

        // Now that we have the model names in an array we can just sort them and
        // use the implode function to join them together with an underscores,
        // which is typically used by convention within the database system.
        sort($models);

        return strtolower(implode('_', $models));
    }

    /**
     * Determine if the model touches a given relation.
     *
     * @param  string  $relation
     * @return bool
     */
    public function touches($relation)
    {
        return in_array($relation, $this->touches);
    }

    /**
     * Touch the owning relations of the model.
     *
     * @return void
     */
    public function touchOwners()
    {
        foreach ($this->touches as $relation) {
            $this->$relation()->touch();

            if ($this->$relation instanceof self) {
                $this->$relation->fireModelEvent('saved', false);

                $this->$relation->touchOwners();
            } elseif ($this->$relation instanceof Collection) {
                $this->$relation->each(function (Model $relation) {
                    $relation->touchOwners();
                });
            }
        }
    }

    /**
     * Get the polymorphic relationship columns.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return array
     */
    protected function getMorphs($name, $type, $id)
    {
        return [$type ?: $name.'_type', $id ?: $name.'_id'];
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        $morphMap = Relation::morphMap();

        if (! empty($morphMap) && in_array(static::class, $morphMap)) {
            return array_search(static::class, $morphMap, true);
        }

        return static::class;
    }

    /**
     * Create a new model instance for a related model.
     *
     * @param  string  $class
     * @return mixed
     */
    protected function newRelatedInstance($class)
    {
        return tap(new $class, function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }

    /**
     * Determine if the given relation is loaded.
     *
     * @param  string  $key
     * @return bool
     */
    public function relationLoaded($key)
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Get the relationships that are touched on save.
     *
     * @return array
     */
    public function getTouchedRelations()
    {
        return $this->touches;
    }

    /**
     * Set the relationships that are touched on save.
     *
     * @param  array  $touches
     * @return $this
     */
    public function setTouchedRelations(array $touches)
    {
        $this->touches = $touches;

        return $this;
    }
}
