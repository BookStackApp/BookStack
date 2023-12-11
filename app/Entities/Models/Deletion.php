<?php

namespace BookStack\Entities\Models;

use BookStack\Activity\Models\Loggable;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int       $id
 * @property int       $deleted_by
 * @property string    $deletable_type
 * @property int       $deletable_id
 * @property Deletable $deletable
 */
class Deletion extends Model implements Loggable
{
    protected $hidden = [];

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
    public function getUrl(string $path = 'restore'): string
    {
        return url("/settings/recycle-bin/{$this->id}/" . ltrim($path, '/'));
    }
}
