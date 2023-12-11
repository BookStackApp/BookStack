<?php

namespace BookStack\Translation;

use BookStack\Users\Models\User;
use Illuminate\Http\Request;

class LocaleManager
{
    /**
     * Array of right-to-left locale options.
     */
    protected array $rtlLocales = ['ar', 'fa', 'he'];

    /**
     * Map of BookStack locale names to best-estimate ISO locale names.
     * Locales can often be found by running `locale -a` on a linux system.
     *
     * @var array<string, string>
     */
    protected array $localeMap = [
        'ar'          => 'ar',
        'bg'          => 'bg_BG',
        'bs'          => 'bs_BA',
        'ca'          => 'ca',
        'cs'          => 'cs_CZ',
        'cy'          => 'cy_GB',
        'da'          => 'da_DK',
        'de'          => 'de_DE',
        'de_informal' => 'de_DE',
        'el'          => 'el_GR',
        'en'          => 'en_GB',
        'es'          => 'es_ES',
        'es_AR'       => 'es_AR',
        'et'          => 'et_EE',
        'eu'          => 'eu_ES',
        'fa'          => 'fa_IR',
        'fi'          => 'fi_FI',
        'fr'          => 'fr_FR',
        'he'          => 'he_IL',
        'hr'          => 'hr_HR',
        'hu'          => 'hu_HU',
        'id'          => 'id_ID',
        'it'          => 'it_IT',
        'ja'          => 'ja',
        'ka'          => 'ka_GE',
        'ko'          => 'ko_KR',
        'lt'          => 'lt_LT',
        'lv'          => 'lv_LV',
        'nb'          => 'nb_NO',
        'nl'          => 'nl_NL',
        'nn'          => 'nn_NO',
        'pl'          => 'pl_PL',
        'pt'          => 'pt_PT',
        'pt_BR'       => 'pt_BR',
        'ro'          => 'ro_RO',
        'ru'          => 'ru',
        'sk'          => 'sk_SK',
        'sl'          => 'sl_SI',
        'sq'          => 'sq_AL',
        'sv'          => 'sv_SE',
        'tr'          => 'tr_TR',
        'uk'          => 'uk_UA',
        'uz'          => 'uz_UZ',
        'vi'          => 'vi_VN',
        'zh_CN'       => 'zh_CN',
        'zh_TW'       => 'zh_TW',
    ];

    /**
     * Get the BookStack locale string for the given user.
     */
    protected function getLocaleForUser(User $user): string
    {
        $default = config('app.default_locale');

        if ($user->isGuest() && config('app.auto_detect_locale')) {
            return $this->autoDetectLocale(request(), $default);
        }

        return setting()->getUser($user, 'language', $default);
    }

    /**
     * Get a locale definition for the current user.
     */
    public function getForUser(User $user): LocaleDefinition
    {
        $localeString = $this->getLocaleForUser($user);

        return new LocaleDefinition(
            $localeString,
            $this->localeMap[$localeString] ?? $localeString,
            in_array($localeString, $this->rtlLocales),
        );
    }

    /**
     * Autodetect the visitors locale by matching locales in their headers
     * against the locales supported by BookStack.
     */
    protected function autoDetectLocale(Request $request, string $default): string
    {
        $availableLocales = $this->getAllAppLocales();

        foreach ($request->getLanguages() as $lang) {
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }

        return $default;
    }

    /**
     * Get all the available app-specific level locale strings.
     */
    public function getAllAppLocales(): array
    {
        return array_keys($this->localeMap);
    }
}
