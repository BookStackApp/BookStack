<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['name', 'html', 'priority'];

    public function book()
    {
        return $this->belongsTo('Oxbow\Book');
    }

    public function getUrl()
    {
        return '/books/' . $this->book->slug . '/' . $this->slug;
    }
}
