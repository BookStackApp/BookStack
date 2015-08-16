<?php namespace Oxbow\Services\Facades;


use Illuminate\Support\Facades\Facade;

class Activity extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'activity'; }
}