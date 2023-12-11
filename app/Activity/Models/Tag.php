<?php

namespace BookStack\Activity\Models;

use BookStack\App\Model;
use BookStack\Permissions\Models\JointPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int    $id
 * @property string $name
 * @property string $value
 * @property int    $order
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'order'];
    protected $hidden = ['id', 'entity_id', 'entity_type', 'created_at', 'updated_at'];

    /**
     * Get the entity that this tag belongs to.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'entity_id')
            ->whereColumn('tags.entity_type', '=', 'joint_permissions.entity_type');
    }

    /**
     * Get a full URL to start a tag name search for this tag name.
     */
    public function nameUrl(): string
    {
        return url('/search?term=%5B' . urlencode($this->name) . '%5D');
    }

    /**
     * Get a full URL to start a tag name and value search for this tag's values.
     */
    public function valueUrl(): string
    {
        return url('/search?term=%5B' . urlencode($this->name) . '%3D' . urlencode($this->value) . '%5D');
    }
}
