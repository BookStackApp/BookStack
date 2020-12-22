<?php namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\PageContent;
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

    protected $hidden = ['html', 'markdown', 'text', 'restricted', 'pivot', 'deleted_at'];

    protected $casts = [
        'draft' => 'boolean',
        'template' => 'boolean',
    ];

    /**
     * Get the entities that are visible to the current user.
     */
    public function scopeVisible(Builder $query): Builder
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
     * Get the url of this page.
     */
    public function getUrl($path = ''): string
    {
        $parts = [
            'books',
            urlencode($this->getAttribute('bookSlug') ?? $this->book->slug),
            $this->draft ? 'draft' : 'page',
            $this->draft ? $this->id : urlencode($this->slug),
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Get the current revision for the page if existing
     * @return PageRevision|null
     */
    public function getCurrentRevision()
    {
        return $this->revisions()->first();
    }

    /**
     * Get this page for JSON display.
     */
    public function forJsonDisplay(): Page
    {
        $refreshed = $this->refresh()->unsetRelations()->load(['tags', 'createdBy', 'updatedBy']);
        $refreshed->setHidden(array_diff($refreshed->getHidden(), ['html', 'markdown']));
        $refreshed->html = (new PageContent($refreshed))->render();
        return $refreshed;
    }

    public function getCoverImage(): string
    {
        $dom = new \DomDocument();
        $dom->loadHTML($this->html);
        $images = $dom->getElementsByTagName('img');

        try {
            $cover = $images->length > 0 ? $images[0]->getAttribute('src') : $this->book->getBookCover();
        } catch (Exception $err) {
            $cover = $this->book->getBookCover();
        }
        return $cover;
    }
}
