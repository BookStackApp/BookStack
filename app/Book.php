<?php namespace BookStack;

class Book extends Entity
{

    protected $fillable = ['name', 'description'];

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

}
