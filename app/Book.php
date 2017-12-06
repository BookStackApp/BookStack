<?php namespace BookStack;

class Book extends Entity
{

    protected $fillable = ['name', 'description', 'image_id'];

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
    
    /**
     * Returns book cover image, if book cover not exists return default cover image.
     * @param int $height - Height of the image
     * @param int $width - Width of the image
     * @return string
     */
    public function getBookCover($height = 170, $width = 300)
    {
        $default = baseUrl('/book_default_cover.png');
        if (!$this->image_id) return $default;

        try {
            $cover = $this->cover ? baseUrl($this->cover->getThumb($width, $height, false)) : $default;
        } catch (\Exception $err) {
            $cover = $default;
        }
        return $cover;
    }
    
    /**
     * Get the cover image of the book
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cover()
    {
        return $this->belongsTo(Image::class, 'image_id');
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
