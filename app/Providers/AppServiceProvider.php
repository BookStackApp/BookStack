<?php namespace BookStack\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Validator;

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
        Validator::extend('is_image', function($attribute, $value, $parameters, $validator) {
            $imageMimes = ['image/png', 'image/bmp', 'image/gif', 'image/jpeg', 'image/jpg', 'image/tiff', 'image/webp'];
            return in_array($value->getMimeType(), $imageMimes);
        });

        Carbon::setLocale(config('app.locale'));
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
