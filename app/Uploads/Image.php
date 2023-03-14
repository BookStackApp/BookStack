<?php

namespace BookStack\Uploads;

use BookStack\Auth\Permissions\JointPermission;
use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Entities\Models\Page;
use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $name
 * @property string $url
 * @property string $path
 * @property string $type
 * @property int    $uploaded_to
 * @property int    $created_by
 * @property int    $updated_by
 */
class Image extends Model
{
    use HasFactory;
    use HasCreatorAndUpdater;

    protected $fillable = ['name'];
    protected $hidden = [];

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'uploaded_to')
            ->where('joint_permissions.entity_type', '=', 'page');
    }

    /**
     * Scope the query to just the images visible to the user based upon the
     * user visibility of the uploaded_to page.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return app()->make(PermissionApplicator::class)->restrictPageRelationQuery($query, 'images', 'uploaded_to');
    }

    /**
     * Get a thumbnail for this image.
     *
     * @throws \Exception
     */
    public function getThumb(?int $width, ?int $height, bool $keepRatio = false): string
    {
        return app()->make(ImageService::class)->getThumbnail($this, $width, $height, $keepRatio);
    }

    /**
     * Get the page this image has been uploaded to.
     * Only applicable to gallery or drawio image types.
     */
    public function getPage(): ?Page
    {
        return $this->belongsTo(Page::class, 'uploaded_to')->first();
    }
}
