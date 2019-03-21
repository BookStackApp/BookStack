<?php namespace BookStack\Providers;

use Blade;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Page;
use BookStack\Settings\Setting;
use BookStack\Settings\SettingService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\ServiceProvider;
use Schema;
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
        Validator::extend('image_extension', function ($attribute, $value, $parameters, $validator) {
            $validImageExtensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tiff', 'webp'];
            return in_array(strtolower($value->getClientOriginalExtension()), $validImageExtensions);
        });


        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        // Allow longer string lengths after upgrade to utf8mb4
        Schema::defaultStringLength(191);

        // Set morph-map due to namespace changes
        Relation::morphMap([
            'BookStack\\Bookshelf' => Bookshelf::class,
            'BookStack\\Book' => Book::class,
            'BookStack\\Chapter' => Chapter::class,
            'BookStack\\Page' => Page::class,
        ]);
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
