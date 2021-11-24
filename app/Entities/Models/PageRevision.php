<?php

namespace BookStack\Entities\Models;

use BookStack\Auth\User;
use BookStack\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PageRevision.
 *
 * @property int    $page_id
 * @property string $slug
 * @property string $book_slug
 * @property int    $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $type
 * @property string $summary
 * @property string $markdown
 * @property string $html
 * @property int    $revision_number
 * @property Page   $page
 * @property-read ?User $createdBy
 */
class PageRevision extends Model
{
    protected $fillable = ['name', 'html', 'text', 'markdown', 'summary'];

    /**
     * Get the user that created the page revision.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page this revision originates from.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the url for this revision.
     *
     * @param null|string $path
     *
     * @return string
     */
    public function getUrl($path = null)
    {
        $url = $this->page->getUrl() . '/revisions/' . $this->id;
        if ($path) {
            return $url . '/' . trim($path, '/');
        }

        return $url;
    }

    /**
     * Get the previous revision for the same page if existing.
     */
    public function getPrevious(): ?PageRevision
    {
        $id = static::newQuery()->where('page_id', '=', $this->page_id)
            ->where('id', '<', $this->id)
            ->max('id');

        if ($id) {
            return static::query()->find($id);
        }

        return null;
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Included here to align with entities in similar use cases.
     * (Yup, Bit of an awkward hack).
     *
     * @deprecated Use instanceof instead.
     */
    public static function isA(string $type): bool
    {
        return $type === 'revision';
    }
}
