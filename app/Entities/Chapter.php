<?php namespace BookStack\Entities;

use Illuminate\Support\Collection;

/**
 * Class Chapter
 * @property Collection<Page> $pages
 */
class Chapter extends BookChild
{
    public $searchFactor = 1.3;

    protected $fillable = ['name', 'description', 'priority', 'book_id'];
    protected $hidden = ['restricted', 'pivot'];

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
     * Get the visible pages in this chapter.
     */
    public function getVisiblePages(): Collection
    {
        return $this->pages()->visible()
        ->orderBy('draft', 'desc')
        ->orderBy('priority', 'asc')
        ->get();
    }
}
