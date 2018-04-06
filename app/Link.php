<?php namespace BookStack;

class Link extends Entity
{
    protected $fillable = ['name', 'link_to', 'priority', 'book_id'];

    protected $with = ['book'];
    public $textField = 'text';

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function hasChapter()
    {
        return $this->chapter()->count() > 0;
    }

    public function getUrl($path = false)
    {
        $bookSlug = $this->getAttribute('bookSlug') ? $this->getAttribute('bookSlug') : $this->book->slug;
        if ($path !== false) {
            return baseUrl('/books/' . urlencode($bookSlug) . '/link/' . urlencode($this->slug) . '/' . trim($path, '/'));
        }
        return baseUrl('/books/' . urlencode($bookSlug) . '/link/' . urlencode($this->slug));
    }

    /**
     * Return a generalised, common raw query that can be 'unioned' across entities.
     * @return string
     */
    public function entityRawQuery()
    {
        return "
                'BookStack\\\\Link' as entity_type, 
                id, 
                id as entity_id, 
                slug,
                name,
                '' as text,
                link_to as html, 
                book_id, 
                priority, 
                '0' as chapter_id,
                '0' as draft,
                '0' as created_by, 
                '0' as updated_by, 
                updated_at, 
                created_at";
    }

}

?>