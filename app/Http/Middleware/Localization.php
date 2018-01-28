<?php namespace BookStack\Http\Middleware;

use Carbon\Carbon;
use Closure;

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
        if (user()->isDefault()) {
            $locale = $defaultLang;
            $availableLocales = config('app.locales');
            foreach ($request->getLanguages() as $lang) {
                if (!in_array($lang, $availableLocales)) {
                    continue;
                }
                $locale = $lang;
                break;
            }
        } else {
            $locale = setting()->getUser(user(), 'language', $defaultLang);
        }
        app()->setLocale($locale);
        Carbon::setLocale($locale);
        return $next($request);
    }
}
