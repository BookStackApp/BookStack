<?php namespace BookStack\Entities\Models;

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
     */
    public function getUrl($path = ''): string
    {
        $parts = [
            'books',
            urlencode($this->getAttribute('bookSlug') ?? $this->book->slug),
            'chapter',
            urlencode($this->slug),
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Check if this chapter has any child pages.
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->pages) > 0;
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
