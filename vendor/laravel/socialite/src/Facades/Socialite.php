<?php

namespace Laravel\Socialite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laravel\Socialite\SocialiteManager
 */
class Socialite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Laravel\Socialite\Contracts\Factory';
    }
}
