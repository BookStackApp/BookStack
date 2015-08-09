<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
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
        return $this->belongsTo('Oxbow\Book');
    }

    public function chapter()
    {
        return $this->belongsTo('Oxbow\Chapter');
    }

    public function hasChapter()
    {
        return $this->chapter()->count() > 0;
    }

    public function createdBy()
    {
        return $this->belongsTo('Oxbow\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('Oxbow\User', 'updated_by');
    }

    public function revisions()
    {
        return $this->hasMany('Oxbow\PageRevision')->orderBy('created_at', 'desc');
    }

    public function getUrl()
    {
        return '/books/' . $this->book->slug . '/page/' . $this->slug;
    }

    public function getExcerpt($length = 100)
    {
        return strlen($this->text) > $length ? substr($this->text, 0, $length-3) . '...' : $this->text;
    }

}
