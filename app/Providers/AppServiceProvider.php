<?php namespace BookStack\Providers;

use BookStack\Services\SettingService;
use BookStack\Setting;
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
        Validator::extend('image', function ($attribute, $value, $parameters, $validator) {
            $imageMimes = ['image/png', 'image/bmp', 'image/gif', 'image/jpeg', 'image/jpg', 'image/tiff', 'image/webp'];
            return in_array($value->getMimeType(), $imageMimes);
        });

        \Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        // Allow longer string lengths after upgrade to utf8mb4
        \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService($app->make(Setting::class), $app->make('Illuminate\Contracts\Cache\Repository'));
        });
    }
}
