<?php

namespace BookStack\Entities\Models;

use BookStack\Auth\User;
use BookStack\Interfaces\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Model $deletable
 */
class Deletion extends Model implements Loggable
{
    /**
     * Get the related deletable record.
     */
    public function deletable(): MorphTo
    {
        return $this->morphTo('deletable')->withTrashed();
    }

    /**
     * Get the user that performed the deletion.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Create a new deletion record for the provided entity.
     */
    public static function createForEntity(Entity $entity): self
    {
        $record = (new self())->forceFill([
            'deleted_by'     => user()->id,
            'deletable_type' => $entity->getMorphClass(),
            'deletable_id'   => $entity->id,
        ]);
        $record->save();

        return $record;
    }

    public function logDescriptor(): string
    {
        $deletable = $this->deletable()->first();

        if ($deletable instanceof Entity) {
            return "Deletion ({$this->id}) for {$deletable->getType()} ({$deletable->id}) {$deletable->name}";
        }

        return "Deletion ({$this->id})";
    }

    /**
     * Get a URL for this specific deletion.
     */
    public function getUrl($path): string
    {
        return url("/settings/recycle-bin/{$this->id}/" . ltrim($path, '/'));
    }
}
