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

    public static array $fields = [
        'draft',
        'template',
        'revision_count',
        'editor',
        'html',
        'text',
        'markdown',
    ];
}
