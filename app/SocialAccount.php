<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{

    protected $fillable = ['user_id', 'driver', 'driver_id', 'timestamps'];

    public function user()
    {
        return $this->belongsTo('BookStack\User');
    }
}
