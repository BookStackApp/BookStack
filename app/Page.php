<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class Page extends Entity
{
    protected $fillable = ['name', 'html', 'priority'];

    protected $simpleAttributes = ['name', 'id', 'slug'];

    public function toSimpleArray()
    {
        $array = array_intersect_key($this->toArray(), array_flip($this->simpleAttributes));
        $array['url'] = $this->getUrl();
        return $array;
    }

    public function book()
    {
        return $this->belongsTo('BookStack\Book');
    }

    public function chapter()
    {
        return $this->belongsTo('BookStack\Chapter');
    }

    public function hasChapter()
    {
        return $this->chapter()->count() > 0;
    }

    public function revisions()
    {
        return $this->hasMany('BookStack\PageRevision')->orderBy('created_at', 'desc');
    }

    public function getUrl()
    {
        $bookSlug = $this->getAttribute('bookSlug') ? $this->getAttribute('bookSlug') : $this->book->slug;
        return '/books/' . $bookSlug . '/page/' . $this->slug;
    }

    public function getExcerpt($length = 100)
    {
        return strlen($this->text) > $length ? substr($this->text, 0, $length-3) . '...' : $this->text;
    }

}
