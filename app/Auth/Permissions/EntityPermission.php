<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $role_id
 * @property int $entity_id
 * @property string $entity_type
 * @property boolean $view
 * @property boolean $create
 * @property boolean $update
 * @property boolean $delete
 */
class EntityPermission extends Model
{
    public const PERMISSIONS = ['view', 'create', 'update', 'delete'];

    protected $fillable = ['role_id', 'view', 'create', 'update', 'delete'];
    public $timestamps = false;

    /**
     * Get this restriction's attached entity.
     */
    public function restrictable(): MorphTo
    {
        return $this->morphTo('restrictable');
    }

    /**
     * Get the role assigned to this entity permission.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
