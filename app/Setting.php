<?php namespace BookStack;

class Setting extends Model
{
    protected $fillable = ['setting_key', 'value'];

    protected $primaryKey = 'setting_key';
}
