<?php

namespace Tests;

use BookStack\Activity\ActivityType;
use BookStack\Translation\LocaleManager;

class LanguageTest extends TestCase
{
    protected array $langs;

    /**
     * LanguageTest constructor.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->langs = array_diff(scandir(lang_path('')), ['..', '.']);
    }

    public function test_locales_list_set_properly()
    {
        $appLocales = $this->app->make(LocaleManager::class)->getAllAppLocales();
        sort($appLocales);
        sort($this->langs);
        $this->assertEquals(implode(':', $this->langs), implode(':', $appLocales), 'app.locales configuration variable does not match those found in lang files');
    }

    // Not part of standard phpunit test runs since we sometimes expect non-added langs.
    public function do_test_locales_all_have_language_dropdown_entry()
    {
        $dropdownLocales = array_keys(trans('settings.language_select', [], 'en'));
        sort($dropdownLocales);
        sort($this->langs);
        $diffs = array_diff($this->langs, $dropdownLocales);
        if (count($diffs) > 0) {
            $diffText = implode(',', $diffs);
            $this->addWarning("Languages: {$diffText} found in files but not in language select dropdown.");
        }
        $this->assertTrue(true);
    }

    public function test_correct_language_if_not_logged_in()
    {
        $loginReq = $this->get('/login');
        $loginReq->assertSee('Log In');

        $loginPageFrenchReq = $this->get('/login', ['Accept-Language' => 'fr']);
        $loginPageFrenchReq->assertSee('Se Connecter');
    }

    public function test_public_lang_autodetect_can_be_disabled()
    {
        config()->set('app.auto_detect_locale', false);
        $loginReq = $this->get('/login');
        $loginReq->assertSee('Log In');

        $loginPageFrenchReq = $this->get('/login', ['Accept-Language' => 'fr']);
        $loginPageFrenchReq->assertDontSee('Se Connecter');
    }

    public function test_all_lang_files_loadable()
    {
        $files = array_diff(scandir(lang_path('en')), ['..', '.']);
        foreach ($this->langs as $lang) {
            foreach ($files as $file) {
                $loadError = false;

                try {
                    $translations = trans(str_replace('.php', '', $file), [], $lang);
                } catch (\Exception $e) {
                    $loadError = true;
                }
                $this->assertFalse($loadError, "Translation file {$lang}/{$file} failed to load");
            }
        }
    }

    public function test_views_use_rtl_if_rtl_language_is_set()
    {
        $this->asEditor()->withHtml($this->get('/'))->assertElementExists('html[dir="ltr"]');

        setting()->putUser($this->users->editor(), 'language', 'ar');

        $this->withHtml($this->get('/'))->assertElementExists('html[dir="rtl"]');
    }

    public function test_unknown_lang_does_not_break_app()
    {
        config()->set('app.locale', 'zz');

        $loginReq = $this->get('/login', ['Accept-Language' => 'zz']);
        $loginReq->assertOk();
        $loginReq->assertSee('Log In');
    }

    public function test_all_activity_types_have_activity_text()
    {
        foreach (ActivityType::all() as $activityType) {
            $langKey = 'activities.' . $activityType;
            $this->assertNotEquals($langKey, trans($langKey, [], 'en'));
        }
    }
}
