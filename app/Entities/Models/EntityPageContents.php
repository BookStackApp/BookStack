<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Model;

class EntityPageContents extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'page_id';
    public $incrementing = false;
}
