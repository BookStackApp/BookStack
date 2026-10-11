<?php

namespace BookStack\App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\Relation;

class Model extends EloquentModel
{
    /**
     * Provides public access to get the raw attribute value from the model.
     * Used in areas where no mutations are required, but performance is critical.
     */
    public function getRawAttribute(string $key): mixed
    {
        return parent::getAttributeFromArray($key);
    }

    /**
     * Modify an ordered relation to be deletable across all database types.
     * This is used to ensure that delete calls don't use the order, and therefore
     * follow the pre-Laravel 13 logic which was MySQL compatible.
     *
     * This only considers standard delete actions, not force deletes.
     *
     * @template T of Relation
     * @param T $relation
     * @return T
     */
    public function deletableOrderedRelation(Relation $relation): Relation
    {
        $relation->onDelete(function (Builder $builder) {
            $builder->toBase()->reorder()->delete();
        });

        return $relation;
    }
}
