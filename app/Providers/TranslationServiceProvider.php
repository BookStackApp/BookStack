<?php

namespace BookStack\Providers;

use BookStack\Translation\FileLoader;
use BookStack\Translation\MessageSelector;
use Illuminate\Translation\TranslationServiceProvider as BaseProvider;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends BaseProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        // This is a tweak upon Laravel's based translation service registration to allow
        // usage of a custom MessageSelector class
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);
            $trans->setSelector(new MessageSelector());

            return $trans;
        });
    }



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
