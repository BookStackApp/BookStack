<?php

namespace BookStack\Entities\Models;

use BookStack\Auth\User;
use BookStack\Interfaces\Loggable;
use BookStack\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PageRevision.
 *
 * @property mixed  $id
 * @property int    $page_id
 * @property string $name
 * @property string $slug
 * @property string $book_slug
 * @property int    $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $type
 * @property string $summary
 * @property string $markdown
 * @property string $html
 * @property string $text
 * @property int    $revision_number
 * @property Page   $page
 * @property-read ?User $createdBy
 */
class PageRevision extends Model implements Loggable
{
    protected $fillable = ['name', 'text', 'summary'];
    protected $hidden = ['html', 'markdown', 'text'];

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
     */
    public function getUrl(string $path = ''): string
    {
        return $this->page->getUrl('/revisions/' . $this->id . '/' . ltrim($path, '/'));
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

    public function logDescriptor(): string
    {
        return "Revision #{$this->revision_number} (ID: {$this->id}) for page ID {$this->page_id}";
    }

    /**
     * Added just to get this to place nice with "Entity" objects.
     *
     * Get the entity type as a simple lowercase word.
     */
    public static function getType(): string
    {
        $className = array_slice(explode('\\', static::class), -1, 1)[0];

        return strtolower($className);
    }
}
