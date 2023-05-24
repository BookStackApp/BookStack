<?php

namespace BookStack\Activity\Models;

use BookStack\App\Model;
use BookStack\Permissions\Models\JointPermission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class View
 * Views are stored per-item per-person within the database.
 * They can be used to find popular items or recently viewed items
 * at a per-person level. They do not record every view instance as an
 * activity. Only the latest and original view times could be recognised.
 *
 * @property int $views
 * @property int $user_id
 */
class View extends Model
{
    protected $fillable = ['user_id', 'views'];

    /**
     * Get all owning viewable models.
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'viewable_id')
            ->whereColumn('views.viewable_type', '=', 'joint_permissions.entity_type');
    }

    /**
     * Increment the current user's view count for the given viewable model.
     */
    public static function incrementFor(Viewable $viewable): int
    {
        $user = user();
        if (is_null($user) || $user->isDefault()) {
            return 0;
        }

        /** @var View $view */
        $view = $viewable->views()->firstOrNew([
            'user_id' => $user->id,
        ], ['views' => 0]);

        $view->forceFill(['views' => $view->views + 1])->save();

        return $view->views;
    }

    /**
     * Clear all views from the system.
     */
    public static function clearAll()
    {
        static::query()->truncate();
    }
}
