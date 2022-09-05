<?php

namespace BookStack\Http\Middleware;

use BookStack\Util\LanguageManager;
use Carbon\Carbon;
use Closure;

class Localization
{
    protected LanguageManager $languageManager;

    public function __construct(LanguageManager $languageManager)
    {
        $this->languageManager = $languageManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get and record the default language in the config
        $defaultLang = config('app.locale');
        config()->set('app.default_locale', $defaultLang);

        // Get the user's language and record that in the config for use in views
        $userLang = $this->languageManager->getUserLanguage($request, $defaultLang);
        config()->set('app.lang', str_replace('_', '-', $this->languageManager->getIsoName($userLang)));

        // Set text direction
        if ($this->languageManager->isRTL($userLang)) {
            config()->set('app.rtl', true);
        }

        app()->setLocale($userLang);
        Carbon::setLocale($userLang);
        $this->languageManager->setPhpDateTimeLocale($userLang);

        return $next($request);
    }
}
