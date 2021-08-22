<?php

namespace BookStack\Providers;

use Blade;
use BookStack\Auth\Access\LoginService;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Entities\BreadcrumbsViewComposer;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Settings\Setting;
use BookStack\Settings\SettingService;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Schema;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set root URL
        $appUrl = config('app.url');
        if ($appUrl) {
            $isHttps = (strpos($appUrl, 'https://') === 0);
            URL::forceRootUrl($appUrl);
            URL::forceScheme($isHttps ? 'https' : 'http');
        }

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        // Allow longer string lengths after upgrade to utf8mb4
        Schema::defaultStringLength(191);

        // Set morph-map due to namespace changes
        Relation::morphMap([
            'BookStack\\Bookshelf' => Bookshelf::class,
            'BookStack\\Book'      => Book::class,
            'BookStack\\Chapter'   => Chapter::class,
            'BookStack\\Page'      => Page::class,
        ]);

        // View Composers
        View::composer('entities.breadcrumbs', BreadcrumbsViewComposer::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService($app->make(Setting::class), $app->make(Repository::class));
        });

        $this->app->singleton(SocialAuthService::class, function ($app) {
            return new SocialAuthService($app->make(SocialiteFactory::class), $app->make(LoginService::class));
        });
    }
}
