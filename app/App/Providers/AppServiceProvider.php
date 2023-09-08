<?php

namespace BookStack\App\Providers;

use BookStack\Access\SocialAuthService;
use BookStack\Activity\Tools\ActivityLogger;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\BookStackExceptionHandlerPage;
use BookStack\Http\HttpRequestService;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Settings\SettingService;
use BookStack\Util\CspService;
use Illuminate\Contracts\Foundation\ExceptionRenderer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Custom container bindings to register.
     * @var string[]
     */
    public $bindings = [
        ExceptionRenderer::class => BookStackExceptionHandlerPage::class,
    ];

    /**
     * Custom singleton bindings to register.
     * @var string[]
     */
    public $singletons = [
        'activity' => ActivityLogger::class,
        SettingService::class => SettingService::class,
        SocialAuthService::class => SocialAuthService::class,
        CspService::class => CspService::class,
        HttpRequestService::class => HttpRequestService::class,
    ];

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
            $isHttps = str_starts_with($appUrl, 'https://');
            URL::forceRootUrl($appUrl);
            URL::forceScheme($isHttps ? 'https' : 'http');
        }

        // Allow longer string lengths after upgrade to utf8mb4
        Schema::defaultStringLength(191);

        // Set morph-map for our relations to friendlier aliases
        Relation::enforceMorphMap([
            'bookshelf' => Bookshelf::class,
            'book'      => Book::class,
            'chapter'   => Chapter::class,
            'page'      => Page::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PermissionApplicator::class, function ($app) {
            return new PermissionApplicator(null);
        });
    }
}
