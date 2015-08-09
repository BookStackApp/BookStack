<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class PageRevision extends Model
{
    protected $fillable = ['name', 'html', 'text'];

    public function createdBy()
    {
        return $this->belongsTo('Oxbow\User', 'created_by');
    }

    public function page()
    {
        return $this->belongsTo('Oxbow\Page');
    }

    public function getUrl()
    {
        return $this->page->getUrl() . '/revisions/' . $this->id;
    }

}
