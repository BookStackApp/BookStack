<?php

namespace BookStack\Users\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $owned_by
 */
trait HasOwner
{
    /**
     * Relation for the user that owns this entity.
     */
    public function ownedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owned_by');
    }
}
