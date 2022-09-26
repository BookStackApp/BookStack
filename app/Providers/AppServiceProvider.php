<?php

namespace BookStack\Providers;

use BookStack\Auth\Access\LoginService;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Entities\BreadcrumbsViewComposer;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\WhoopsBookStackPrettyHandler;
use BookStack\Settings\Setting;
use BookStack\Settings\SettingService;
use BookStack\Util\CspService;
use GuzzleHttp\Client;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Whoops\Handler\HandlerInterface;

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

        // Set morph-map for our relations to friendlier aliases
        Relation::enforceMorphMap([
            'bookshelf' => Bookshelf::class,
            'book'      => Book::class,
            'chapter'   => Chapter::class,
            'page'      => Page::class,
        ]);

        // View Composers
        View::composer('entities.breadcrumbs', BreadcrumbsViewComposer::class);

        // Set paginator to use bootstrap-style pagination
        Paginator::useBootstrap();

        // Setup database upon parallel testing database creation
        ParallelTesting::setUpTestDatabase(function ($database, $token) {
            Artisan::call('db:seed --class=DummyContentSeeder');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HandlerInterface::class, function ($app) {
            return $app->make(WhoopsBookStackPrettyHandler::class);
        });

        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService($app->make(Setting::class), $app->make(Repository::class));
        });

        $this->app->singleton(SocialAuthService::class, function ($app) {
            return new SocialAuthService($app->make(SocialiteFactory::class), $app->make(LoginService::class));
        });

        $this->app->singleton(CspService::class, function ($app) {
            return new CspService();
        });

        $this->app->bind(HttpClientInterface::class, function ($app) {
            return new Client([
                'timeout' => 3,
            ]);
        });
    }
}
