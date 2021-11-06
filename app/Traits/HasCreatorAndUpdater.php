<?php

namespace BookStack\Traits;

use BookStack\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $created_by
 * @property int $updated_by
 */
trait HasCreatorAndUpdater
{
    /**
     * Relation for the user that created this entity.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation for the user that updated this entity.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
