<?php

namespace BookStack\Entities\Models;

use BookStack\Activity\Models\Loggable;
use BookStack\App\Model;
use BookStack\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed  $id
 * @property int    $page_id
 * @property string $name
 * @property string $html
 * @property string $markdown
 * @property string $summary
 * @property int    $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Page   $page
 * @property-read ?User $createdBy
 */
class PageUpdateDraft extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'summary'];
    protected $hidden = ['html', 'markdown', 'text'];

    /**
     * Get the user that created the page update draft.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page this update draft has been created for.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
