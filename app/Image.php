<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function getFilePath()
    {
        return storage_path() . $this->url;
    }

    public function createdBy()
    {
        return $this->belongsTo('Oxbow\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('Oxbow\User', 'updated_by');
    }
}
