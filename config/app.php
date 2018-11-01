<?php

return [

    'env' => env('APP_ENV', 'production'),

    /**
     * Set the default view type for various lists. Can be overridden by user preferences.
     * This will be used for public viewers and users that have not set a preference.
     */
    'views' => [
        'books' => env('APP_VIEWS_BOOKS', 'list')
    ],

    /**
     * The number of revisions to keep in the database.
     * Once this limit is reached older revisions will be deleted.
     * If set to false then a limit will not be enforced.
     */
    'revision_limit' => env('REVISION_LIMIT', 50),

    /**
     * Allow <script> tags to entered within page content.
     * <script> tags are escaped by default.
     * Even when overridden the WYSIWYG editor may still escape script content.
     */
    'allow_content_scripts' => env('ALLOW_CONTENT_SCRIPTS', false),

    /**
     * Override the default behaviour for allowing crawlers to crawl the instance.
     * May be ignored if view has be overridden or modified.
     * Defaults to null since, if not set, 'app-public' status used instead.
     */
    'allow_robots' => env('ALLOW_ROBOTS', null),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', '') === 'http://bookstack.dev' ? '' : env('APP_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LANG', 'en'),
    'locales' => ['en', 'ar', 'de', 'es', 'es_AR', 'fr', 'nl', 'pt_BR', 'sk', 'sv', 'ja', 'pl', 'it', 'ru', 'zh_CN', 'zh_TW'],

    /*
    |--------------------------------------------------------------------------
    | Right-to-left text control
    |--------------------------------------------------------------------------
    |
    | Right-to-left text control is set to false by default since English
    | is the primary supported application but this may be dynamically
    | altered by the applications localization system.
    |
    */

    'rtl' => false,

    /*
    |--------------------------------------------------------------------------
    | Auto-detect the locale for public users
    |--------------------------------------------------------------------------
    |
    | For public users their locale can be guessed by headers sent by their
    | browser. This is usually set by users in their browser settings.
    | If not found the default app locale will be used.
    |
    */
    'auto_detect_locale' => env('APP_AUTO_LANG_PUBLIC', true),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'AbAZchsay4uBTU33RubBzLKw203yqSqr'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOGGING', 'single'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        SocialiteProviders\Manager\ServiceProvider::class,

        /**
         * Third Party
         */
        Intervention\Image\ImageServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        Barryvdh\Snappy\ServiceProvider::class,


        /*
         * Application Service Providers...
         */
        BookStack\Providers\PaginationServiceProvider::class,

        BookStack\Providers\AuthServiceProvider::class,
        BookStack\Providers\AppServiceProvider::class,
        BookStack\Providers\BroadcastServiceProvider::class,
        BookStack\Providers\EventServiceProvider::class,
        BookStack\Providers\RouteServiceProvider::class,
        BookStack\Providers\CustomFacadeProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'       => Illuminate\Support\Facades\App::class,
        'Artisan'   => Illuminate\Support\Facades\Artisan::class,
        'Auth'      => Illuminate\Support\Facades\Auth::class,
        'Blade'     => Illuminate\Support\Facades\Blade::class,
        'Bus'       => Illuminate\Support\Facades\Bus::class,
        'Cache'     => Illuminate\Support\Facades\Cache::class,
        'Config'    => Illuminate\Support\Facades\Config::class,
        'Cookie'    => Illuminate\Support\Facades\Cookie::class,
        'Crypt'     => Illuminate\Support\Facades\Crypt::class,
        'DB'        => Illuminate\Support\Facades\DB::class,
        'Eloquent'  => Illuminate\Database\Eloquent\Model::class,
        'Event'     => Illuminate\Support\Facades\Event::class,
        'File'      => Illuminate\Support\Facades\File::class,
        'Hash'      => Illuminate\Support\Facades\Hash::class,
        'Input'     => Illuminate\Support\Facades\Input::class,
        'Inspiring' => Illuminate\Foundation\Inspiring::class,
        'Lang'      => Illuminate\Support\Facades\Lang::class,
        'Log'       => Illuminate\Support\Facades\Log::class,
        'Mail'      => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password'  => Illuminate\Support\Facades\Password::class,
        'Queue'     => Illuminate\Support\Facades\Queue::class,
        'Redirect'  => Illuminate\Support\Facades\Redirect::class,
        'Redis'     => Illuminate\Support\Facades\Redis::class,
        'Request'   => Illuminate\Support\Facades\Request::class,
        'Response'  => Illuminate\Support\Facades\Response::class,
        'Route'     => Illuminate\Support\Facades\Route::class,
        'Schema'    => Illuminate\Support\Facades\Schema::class,
        'Session'   => Illuminate\Support\Facades\Session::class,
        'Storage'   => Illuminate\Support\Facades\Storage::class,
        'URL'       => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View'      => Illuminate\Support\Facades\View::class,
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,

        /**
         * Third Party
         */

        'ImageTool' => Intervention\Image\Facades\Image::class,
        'DomPDF' => Barryvdh\DomPDF\Facade::class,
        'SnappyPDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,

        /**
         * Custom
         */

        'Activity' => BookStack\Facades\Activity::class,
        'Setting'  => BookStack\Facades\Setting::class,
        'Views'    => BookStack\Facades\Views::class,
        'Images'   => BookStack\Facades\Images::class,

    ],

    'proxies' => env('APP_PROXIES', ''),

];
