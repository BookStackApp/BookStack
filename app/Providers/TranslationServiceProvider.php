<?php

namespace BookStack\Providers;

use BookStack\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider as BaseProvider;

class TranslationServiceProvider extends BaseProvider
{
    /**
     * Register the translation line loader.
     * Overrides the default register action from Laravel so a custom loader can be used.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
        });
    }
}
