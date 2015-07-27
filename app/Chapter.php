<?php namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{

    protected $fillable = ['name', 'description', 'priority', 'book_id'];

    public function book()
    {
        return $this->belongsTo('Oxbow\Book');
    }

    public function children()
    {
        return $this->hasMany('Oxbow\Page')->orderBy('priority', 'ASC');
    }

    public function getUrl()
    {
        return '/books/' . $this->book->slug . '/chapter/' . $this->slug;
    }

}
