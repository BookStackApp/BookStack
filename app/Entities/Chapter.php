<?php namespace BookStack\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends BookChild
{
    public $searchFactor = 1.3;

    protected $fillable = ['name', 'description', 'priority', 'book_id'];

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
        $fullPath = '/books/' . urlencode($bookSlug) . '/chapter/' . urlencode($this->slug);

        if ($path !== false) {
            $fullPath .= '/' . trim($path, '/');
        }

        return url($fullPath);
    }

    /**
     * Get an excerpt of this chapter's description to the specified length or less.
     * @param int $length
     * @return string
     */
    public function getExcerpt(int $length = 100)
    {
        $description = $this->text ?? $this->description;
        return mb_strlen($description) > $length ? mb_substr($description, 0, $length-3) . '...' : $description;
    }

    /**
     * Return a generalised, common raw query that can be 'unioned' across entities.
     * @return string
     */
    public function entityRawQuery()
    {
        return "'BookStack\\\\Chapter' as entity_type, id, id as entity_id, slug, name, {$this->textField} as text, '' as html, book_id, priority, '0' as chapter_id, '0' as draft, created_by, updated_by, updated_at, created_at";
    }

    /**
     * Check if this chapter has any child pages.
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->pages) > 0;
    }
}
