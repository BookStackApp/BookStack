<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class EmailConfirmation extends Model
{
    protected $fillable = ['user_id', 'token'];

    public function user()
    {
        return $this->belongsTo('Oxbow\User');
    }
}
