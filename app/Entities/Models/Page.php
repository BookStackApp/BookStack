<?php namespace BookStack\Entities;

use BookStack\Uploads\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Permissions;

/**
 * Class Page
 * @property int $chapter_id
 * @property string $html
 * @property string $markdown
 * @property string $text
 * @property bool $template
 * @property bool $draft
 * @property int $revision_count
 * @property Chapter $chapter
 * @property Collection $attachments
 */
class Page extends BookChild
{
    protected $fillable = ['name', 'priority', 'markdown'];

    protected $simpleAttributes = ['name', 'id', 'slug'];

    public $textField = 'text';

    protected $hidden = ['html', 'markdown', 'text', 'restricted', 'pivot'];

    /**
     * Get the entities that are visible to the current user.
     */
    public function scopeVisible(Builder $query)
    {
        $query = Permissions::enforceDraftVisiblityOnQuery($query);
        return parent::scopeVisible($query);
    }

    /**
     * Converts this page into a simplified array.
     * @return mixed
     */
    public function toSimpleArray()
    {
        $array = array_intersect_key($this->toArray(), array_flip($this->simpleAttributes));
        $array['url'] = $this->getUrl();
        return $array;
    }

    /**
     * Get the chapter that this page is in, If applicable.
     * @return BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Check if this page has a chapter.
     * @return bool
     */
    public function hasChapter()
    {
        return $this->chapter()->count() > 0;
    }

    /**
     * Get the associated page revisions, ordered by created date.
     * @return mixed
     */
    public function revisions()
    {
        return $this->hasMany(PageRevision::class)->where('type', '=', 'version')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Get the attachments assigned to this page.
     * @return HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_to')->orderBy('order', 'asc');
    }

    /**
     * Get the url for this page.
     * @param string|bool $path
     * @return string
     */
    public function getUrl($path = false)
    {
        $bookSlug = $this->getAttribute('bookSlug') ? $this->getAttribute('bookSlug') : $this->book->slug;
        $midText = $this->draft ? '/draft/' : '/page/';
        $idComponent = $this->draft ? $this->id : urlencode($this->slug);

        $url = '/books/' . urlencode($bookSlug) . $midText . $idComponent;
        if ($path !== false) {
            $url .= '/' . trim($path, '/');
        }

        return url($url);
    }

    /**
     * Get the current revision for the page if existing
     * @return PageRevision|null
     */
    public function getCurrentRevision()
    {
        return $this->revisions()->first();
    }
}
