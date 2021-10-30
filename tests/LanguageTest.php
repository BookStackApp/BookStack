<?php

namespace Tests;

class LanguageTest extends TestCase
{
    protected $langs;

    /**
     * LanguageTest constructor.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->langs = array_diff(scandir(resource_path('lang')), ['..', '.']);
    }

    public function test_locales_config_key_set_properly()
    {
        $configLocales = config('app.locales');
        sort($configLocales);
        sort($this->langs);
        $this->assertEquals(implode(':', $configLocales), implode(':', $this->langs), 'app.locales configuration variable does not match those found in lang files');
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
        $files = array_diff(scandir(resource_path('lang/en')), ['..', '.']);
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

    public function test_rtl_config_set_if_lang_is_rtl()
    {
        $this->asEditor();
        $this->assertFalse(config('app.rtl'), 'App RTL config should be false by default');
        setting()->putUser($this->getEditor(), 'language', 'ar');
        $this->get('/');
        $this->assertTrue(config('app.rtl'), 'App RTL config should have been set to true by middleware');
    }
}
