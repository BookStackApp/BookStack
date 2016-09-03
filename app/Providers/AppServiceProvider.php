<?php namespace BookStack\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Custom validation methods
        \Validator::extend('is_image', function($attribute, $value, $parameters, $validator) {
            $imageMimes = ['image/png', 'image/bmp', 'image/gif', 'image/jpeg', 'image/jpg', 'image/tiff', 'image/webp'];
            return in_array($value->getMimeType(), $imageMimes);
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
