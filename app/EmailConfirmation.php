<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class EmailConfirmation extends Model
{
    protected $fillable = ['user_id', 'token'];

    public function user()
    {
        return $this->belongsTo('BookStack\User');
    }
}
