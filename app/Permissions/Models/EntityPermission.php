<?php

namespace BookStack\Permissions\Models;

use BookStack\App\Model;
use BookStack\Users\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    protected $hidden = ['entity_id', 'entity_type', 'id'];
    protected $casts = [
        'view' => 'boolean',
        'create' => 'boolean',
        'read' => 'boolean',
        'update' => 'boolean',
        'delete' => 'boolean',
    ];

    /**
     * Get the role assigned to this entity permission.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
