<?php namespace BookStack\Entities;

use BookStack\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Deletion extends Model
{

    /**
     * Get the related deletable record.
     */
    public function deletable(): MorphTo
    {
        return $this->morphTo('deletable')->withTrashed();
    }

    /**
     * The the user that performed the deletion.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Create a new deletion record for the provided entity.
     */
    public static function createForEntity(Entity $entity): Deletion
    {
        $record = (new self())->forceFill([
            'deleted_by' => user()->id,
            'deletable_type' => $entity->getMorphClass(),
            'deletable_id' => $entity->id,
        ]);
        $record->save();
        return $record;
    }

}
