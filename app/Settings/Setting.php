<?php namespace BookStack\Settings;

use BookStack\Model;

class Setting extends Model
{
    protected $fillable = ['setting_key', 'value'];

    protected $primaryKey = 'setting_key';
}
