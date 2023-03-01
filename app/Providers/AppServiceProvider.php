<?php

namespace BookStack\Providers;

use BookStack\Actions\ActivityLogger;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\WhoopsBookStackPrettyHandler;
use BookStack\Settings\SettingService;
use BookStack\Util\CspService;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Whoops\Handler\HandlerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Custom container bindings to register.
     * @var string[]
     */
    public $bindings = [
        HandlerInterface::class => WhoopsBookStackPrettyHandler::class,
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
            $isHttps = (strpos($appUrl, 'https://') === 0);
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
        $this->app->bind(HttpClientInterface::class, function ($app) {
            return new Client([
                'timeout' => 3,
                'verify' => config('app.env') !== 'development' // Allows self-signed certificates to work when in dev mode
            ]);
        });
    }
}
