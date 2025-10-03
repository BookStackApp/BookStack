<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $page_id
 */
class EntityPageData extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'page_id';
    public $incrementing = false;
}
