<?php

namespace BookStack\App\Providers;

use BookStack\Access\SocialDriverManager;
use BookStack\Activity\Models\Comment;
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
    public array $bindings = [
        ExceptionRenderer::class => BookStackExceptionHandlerPage::class,
    ];

    /**
     * Custom singleton bindings to register.
     * @var string[]
     */
    public array $singletons = [
        'activity' => ActivityLogger::class,
        SettingService::class => SettingService::class,
        SocialDriverManager::class => SocialDriverManager::class,
        CspService::class => CspService::class,
        HttpRequestService::class => HttpRequestService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionApplicator::class, function ($app) {
            return new PermissionApplicator(null);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->validateEnvironment();

        // Set root URL
        $appUrl = config('app.url');
        if ($appUrl) {
            $isHttps = str_starts_with($appUrl, 'https://');
            URL::forceRootUrl($appUrl);
            URL::forceScheme($isHttps ? 'https' : 'http');
        }

        // Set SMTP mail driver to use a local domain matching the app domain,
        // which helps avoid defaulting to a 127.0.0.1 domain
        if ($appUrl) {
            $hostName = parse_url($appUrl, PHP_URL_HOST) ?: null;
            config()->set('mail.mailers.smtp.local_domain', $hostName);
        }

        // Allow longer string lengths after upgrade to utf8mb4
        Schema::defaultStringLength(191);

        // Set morph-map for our relations to friendlier aliases
        Relation::enforceMorphMap([
            'bookshelf' => Bookshelf::class,
            'book'      => Book::class,
            'chapter'   => Chapter::class,
            'page'      => Page::class,
            'comment'   => Comment::class,
        ]);
    }

    /**
     * Validate critical environment configuration.
     * Fails fast if required configuration is missing in production.
     */
    protected function validateEnvironment(): void
    {
        $appKey = config('app.key');
        $appEnv = config('app.env', 'production');

        // Fail fast if APP_KEY is not set
        if (empty($appKey)) {
            $message = 'APP_KEY is not set. Run `php artisan key:generate` to create one.';

            if ($appEnv === 'production') {
                throw new \RuntimeException(
                    'SECURITY ERROR: ' . $message . ' Application cannot start in production without APP_KEY.'
                );
            }

            // In non-production, just warn but don't fail
            if ($appEnv !== 'testing') {
                logger()->warning('APP_KEY not set. ' . $message);
            }
        }

        // Warn if APP_KEY doesn't have the base64 prefix (Laravel format)
        if (!empty($appKey) && !str_starts_with($appKey, 'base64:')) {
            $message = 'APP_KEY should be prefixed with "base64:" for proper encoding. ' .
                'Run `php artisan key:generate` to create a valid key.';

            if ($appEnv === 'production') {
                logger()->error('SECURITY WARNING: ' . $message);
            } elseif ($appEnv !== 'testing') {
                logger()->warning($message);
            }
        }
    }
}
