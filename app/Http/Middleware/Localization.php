<?php namespace BookStack\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $defaultLang = config('app.locale');

        if (user()->isDefault() && config('app.auto_detect_locale')) {
            $locale = $this->autoDetectLocale($request, $defaultLang);
        } else {
            $locale = setting()->getUser(user(), 'language', $defaultLang);
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        return $next($request);
    }

    /**
     * Autodetect the visitors locale by matching locales in their headers
     * against the locales supported by BookStack.
     * @param Request $request
     * @param string $default
     * @return string
     */
    protected function autoDetectLocale(Request $request, string $default)
    {
        $availableLocales = config('app.locales');
        foreach ($request->getLanguages() as $lang) {
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }
        return $default;
    }
}
