<?php namespace BookStack\Entities;

use BookStack\Uploads\Attachment;

class Page extends Entity
{
    protected $fillable = ['name', 'html', 'priority', 'markdown'];

    protected $simpleAttributes = ['name', 'id', 'slug'];

    public $textField = 'text';

    /**
     * Get the morph class for this model.
     * @return string
     */
    public function getMorphClass()
    {
        return 'BookStack\\Page';
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
     * Get the book this page sits in.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the parent item
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->chapter_id ? $this->chapter() : $this->book();
    }

    /**
     * Get the chapter that this page is in, If applicable.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
        return $this->hasMany(PageRevision::class)->where('type', '=', 'version')->orderBy('created_at', 'desc');
    }

    /**
     * Get the attachments assigned to this page.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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

        if ($path !== false) {
            return baseUrl('/books/' . urlencode($bookSlug) . $midText . $idComponent . '/' . trim($path, '/'));
        }

        return baseUrl('/books/' . urlencode($bookSlug) . $midText . $idComponent);
    }

    /**
     * Get an excerpt of this page's content to the specified length.
     * @param int $length
     * @return mixed
     */
    public function getExcerpt($length = 100)
    {
        $text = strlen($this->text) > $length ? substr($this->text, 0, $length-3) . '...' : $this->text;
        return mb_convert_encoding($text, 'UTF-8');
    }

    /**
     * Return a generalised, common raw query that can be 'unioned' across entities.
     * @param bool $withContent
     * @return string
     */
    public function entityRawQuery($withContent = false)
    {
        $htmlQuery = $withContent ? 'html' : "'' as html";
        return "'BookStack\\\\Page' as entity_type, id, id as entity_id, slug, name, {$this->textField} as text, {$htmlQuery}, book_id, priority, chapter_id, draft, created_by, updated_by, updated_at, created_at";
    }

    /**
     * Get the current revision for the page if existing
     * @return \BookStack\Entities\PageRevision|null
     */
    public function getCurrentRevision()
    {
        return $this->revisions()->first();
    }
}
