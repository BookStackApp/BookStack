<?php

namespace BookStack\Uploads;

use BookStack\Auth\Permissions\JointPermission;
use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property ?Page  $page
 * @property bool   $external
 * @property int    $uploaded_to
 * @property User   $updatedBy
 * @property User   $createdBy
 *
 * @method static Entity|Builder visible()
 */
class Attachment extends Model
{
    use HasCreatorAndUpdater;

    protected $fillable = ['name', 'order'];
    protected $hidden = ['path', 'page'];
    protected $casts = [
        'external' => 'bool',
    ];

    /**
     * Get the downloadable file name for this upload.
     *
     * @return mixed|string
     */
    public function getFileName()
    {
        if (strpos($this->name, '.') !== false) {
            return $this->name;
        }

        return $this->name . '.' . $this->extension;
    }

    /**
     * Get the page this file was uploaded to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'uploaded_to');
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'uploaded_to')
            ->where('joint_permissions.entity_type', '=', 'page');
    }

    /**
     * Get the url of this file.
     */
    public function getUrl($openInline = false): string
    {
        if ($this->external && strpos($this->path, 'http') !== 0) {
            return $this->path;
        }

        return url('/attachments/' . $this->id . ($openInline ? '?open=true' : ''));
    }

    /**
     * Generate a HTML link to this attachment.
     */
    public function htmlLink(): string
    {
        return '<a target="_blank" href="' . e($this->getUrl()) . '">' . e($this->name) . '</a>';
    }

    /**
     * Generate a markdown link to this attachment.
     */
    public function markdownLink(): string
    {
        return '[' . $this->name . '](' . $this->getUrl() . ')';
    }

    /**
     * Scope the query to those attachments that are visible based upon related page permissions.
     */
    public function scopeVisible(): Builder
    {
        $permissions = app()->make(PermissionApplicator::class);

        return $permissions->restrictPageRelationQuery(
            self::query(),
            'attachments',
            'uploaded_to'
        );
    }
}
