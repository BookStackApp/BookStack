<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Contracts\Database\ModelIdentifier;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

trait SerializesAndRestoresModelIdentifiers
{
    /**
     * Get the property value prepared for serialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getSerializedPropertyValue($value)
    {
        if ($value instanceof QueueableCollection) {
            return new ModelIdentifier($value->getQueueableClass(), $value->getQueueableIds());
        }

        if ($value instanceof QueueableEntity) {
            return new ModelIdentifier(get_class($value), $value->getQueueableId());
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getRestoredPropertyValue($value)
    {
        if (! $value instanceof ModelIdentifier) {
            return $value;
        }

        return is_array($value->id)
                ? $this->restoreCollection($value)
                : $this->getQueryForModelRestoration(new $value->class)
                            ->useWritePdo()->findOrFail($value->id);
    }

    /**
     * Restore a queueable collection instance.
     *
     * @param  \Illuminate\Contracts\Database\ModelIdentifier  $value
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function restoreCollection($value)
    {
        if (! $value->class || count($value->id) === 0) {
            return new EloquentCollection;
        }

        $model = new $value->class;

        return $this->getQueryForModelRestoration($model)->useWritePdo()
                    ->whereIn($model->getQualifiedKeyName(), $value->id)->get();
    }

    /**
     * Get the query for restoration.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQueryForModelRestoration($model)
    {
        return $model->newQueryWithoutScopes();
    }
}
