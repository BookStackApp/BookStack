<?php namespace BookStack\Api;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = ['name', 'expires_at'];

}
