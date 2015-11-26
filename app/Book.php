<?php

namespace BookStack;

class Book extends Entity
{

    protected $fillable = ['name', 'description'];

    public function getUrl()
    {
        return '/books/' . $this->slug;
    }

    public function getEditUrl()
    {
        return $this->getUrl() . '/edit';
    }

    public function pages()
    {
        return $this->hasMany('BookStack\Page');
    }

    public function chapters()
    {
        return $this->hasMany('BookStack\Chapter');
    }

    public function getExcerpt($length = 100)
    {
        return strlen($this->description) > $length ? substr($this->description, 0, $length-3) . '...' : $this->description;
    }

}
