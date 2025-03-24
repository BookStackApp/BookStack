<?php

namespace BookStack\Search\Vectors;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $entity_type
 * @property int $entity_id
 * @property string $text
 * @property string $embedding
 */
class SearchVector extends Model
{
    public $timestamps = false;
}
