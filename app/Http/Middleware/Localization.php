<?php namespace BookStack\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class Localization
{

    /**
     * Array of right-to-left locales
     * @var array
     */
    protected $rtlLocales = ['ar'];

    /**
     * Map of BookStack locale names to best-estimate system locale names.
     * @var array
     */
    protected $localeMap = [
        'ar' => 'ar',
        'da' => 'da_DK',
        'de' => 'de_DE',
        'de_informal' => 'de_DE',
        'en' => 'en_GB',
        'es' => 'es_ES',
        'es_AR' => 'es_AR',
        'fr' => 'fr_FR',
        'it' => 'it_IT',
        'ja' => 'ja',
        'ko' => 'ko_KR',
        'nl' => 'nl_NL',
        'pl' => 'pl_PL',
        'pt_BR' => 'pt_BR',
        'ru' => 'ru',
        'sk' => 'sk_SK',
        'sv' => 'sv_SE',
        'uk' => 'uk_UA',
        'zh_CN' => 'zh_CN',
        'zh_TW' => 'zh_TW',
        'tr' => 'tr_TR',
    ];

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
        config()->set('app.default_locale', $defaultLang);

        if (user()->isDefault() && config('app.auto_detect_locale')) {
            $locale = $this->autoDetectLocale($request, $defaultLang);
        } else {
            $locale = setting()->getUser(user(), 'language', $defaultLang);
        }

        config()->set('app.lang', str_replace('_', '-', $this->getLocaleIso($locale)));

        // Set text direction
        if (in_array($locale, $this->rtlLocales)) {
            config()->set('app.rtl', true);
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        $this->setSystemDateLocale($locale);
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

    /**
     * Get the ISO version of a BookStack language name
     * @param  string $locale
     * @return string
     */
    public function getLocaleIso(string $locale)
    {
        return $this->localeMap[$locale] ?? $locale;
    }

    /**
     * Set the system date locale for localized date formatting.
     * Will try both the standard locale name and the UTF8 variant.
     * @param string $locale
     */
    protected function setSystemDateLocale(string $locale)
    {
        $systemLocale = $this->getLocaleIso($locale);
        $set = setlocale(LC_TIME, $systemLocale);
        if ($set === false) {
            setlocale(LC_TIME, $systemLocale . '.utf8');
        }
    }
}
