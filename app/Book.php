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

    public function chapters()
    {
        return $this->hasMany('Oxbow\Chapter');
    }

    public function createdBy()
    {
        return $this->belongsTo('Oxbow\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('Oxbow\User', 'updated_by');
    }

    public function children()
    {
        $pages = $this->pages()->where('chapter_id', '=', 0)->get();
        $chapters = $this->chapters()->get();
        foreach($chapters as $chapter) {
            $pages->push($chapter);
        }
        return $pages->sortBy('priority');
    }

}
