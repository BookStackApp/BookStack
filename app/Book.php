<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
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
        return $this->hasMany('Oxbow\Page');
    }

}
