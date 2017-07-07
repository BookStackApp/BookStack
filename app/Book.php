<?php namespace BookStack;

class Book extends Entity
{

    protected $fillable = ['name', 'description', 'image'];

    /**
     * Get the url for this book.
     * @param string|bool $path
     * @return string
     */
    public function getUrl($path = false)
    {
        if ($path !== false) {
            return baseUrl('/books/' . urlencode($this->slug) . '/' . trim($path, '/'));
        }
        return baseUrl('/books/' . urlencode($this->slug));
    }

    public function getBookCover($size = 120)
    {
        $default = baseUrl('/default.png');
        $image = $this->image;
        if ($image === 0 || $image === '0' || $image === null) 
            return $default;
        try {
            $cover = $this->cover ? baseUrl($this->cover->getThumb(120, 192, false)) : $default;
        } catch (\Exception $err) {
            $cover = $default;
        }
        return $cover;
    }

    public function cover()
    {
        return $this->belongsTo(Image::class, 'image');
    }
    /*
     * Get the edit url for this book.
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl() . '/edit';
    }

    /**
     * Get all pages within this book.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get all chapters within this book.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Get an excerpt of this book's description to the specified length or less.
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
        return "'BookStack\\\\Book' as entity_type, id, id as entity_id, slug, name, {$this->textField} as text,'' as html, '0' as book_id, '0' as priority, '0' as chapter_id, '0' as draft, created_by, updated_by, updated_at, created_at";
    }

}
