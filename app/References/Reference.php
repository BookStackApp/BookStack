<?php

namespace BookStack\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $from_id
 * @property string $from_type
 * @property int $to_id
 * @property string $to_type
 */
class Reference extends Model
{
    public function from(): MorphTo
    {
        return $this->morphTo('from');
    }

    public function to(): MorphTo
    {
        return $this->morphTo('to');
    }
}
