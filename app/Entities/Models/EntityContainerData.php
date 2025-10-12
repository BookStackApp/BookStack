<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $entity_id
 * @property string  $entity_type
 * @property string  $description
 * @property string  $description_html
 * @property ?int    $default_template_id
 * @property ?int    $image_id
 * @property ?int    $sort_rule_id
 */
class EntityContainerData extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'entity_id';
    public $incrementing = false;

    public static array $fields = [
        'description',
        'description_html',
        'default_template_id',
        'image_id',
        'sort_rule_id',
    ];

    /**
     * Override the default set keys for save query method to make it work with composite keys.
     */
    public function setKeysForSaveQuery($query): Builder
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery())
            ->where('entity_type', '=', $this->entity_type);

        return $query;
    }

    /**
     * Override the default set keys for a select query method to make it work with composite keys.
     */
    protected function setKeysForSelectQuery($query): Builder
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSelectQuery())
            ->where('entity_type', '=', $this->entity_type);

        return $query;
    }
}
