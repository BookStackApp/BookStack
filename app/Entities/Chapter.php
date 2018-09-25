<?php namespace BookStack\Entities;

use BookStack\Entities\Book;
use BookStack\Entities\Entity;
use BookStack\Entities\Page;

class Chapter extends Entity
{
    public $searchFactor = 1.3;

    protected $fillable = ['name', 'description', 'priority', 'book_id'];

    /**
     * Get the morph class for this model.
     * @return string
     */
    public function getMorphClass()
    {
        return 'BookStack\\Chapter';
    }

    /**
     * Get the book this chapter is within.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the pages that this chapter contains.
     * @param string $dir
     * @return mixed
     */
    public function pages($dir = 'ASC')
    {
        return $this->hasMany(Page::class)->orderBy('priority', $dir);
    }

    /**
     * Get the url of this chapter.
     * @param string|bool $path
     * @return string
     */
    public function getUrl($path = false)
    {
        $bookSlug = $this->getAttribute('bookSlug') ? $this->getAttribute('bookSlug') : $this->book->slug;
        if ($path !== false) {
            return baseUrl('/books/' . urlencode($bookSlug) . '/chapter/' . urlencode($this->slug) . '/' . trim($path, '/'));
        }
        return baseUrl('/books/' . urlencode($bookSlug) . '/chapter/' . urlencode($this->slug));
    }

    /**
     * Get an excerpt of this chapter's description to the specified length or less.
     * @param int $length
     * @return string
     */
    public function getExcerpt($length = 100)
    {
        $description = $this->description;
        return strlen($description) > $length ? substr($description, 0, $length-3) . '...' : $description;
    }

    /**
     * Return a generalised, common raw query that can be 'unioned' across entities.
     * @return string
     */
    public function entityRawQuery()
    {
        return "'BookStack\\\\Chapter' as entity_type, id, id as entity_id, slug, name, {$this->textField} as text, '' as html, book_id, priority, '0' as chapter_id, '0' as draft, created_by, updated_by, updated_at, created_at";
    }
}
