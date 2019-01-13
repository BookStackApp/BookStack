<?php namespace Tests;

class LanguageTest extends TestCase
{

    protected $langs;

    /**
     * LanguageTest constructor.
     */
    public function setUp()
    {
        parent::setUp();
        $this->langs = array_diff(scandir(resource_path('lang')), ['..', '.', 'check.php', 'format.php']);
    }

    public function test_locales_config_key_set_properly()
    {
        $configLocales = config('app.locales');
        sort($configLocales);
        sort($this->langs);
        $this->assertTrue(implode(':', $this->langs) === implode(':', $configLocales), 'app.locales configuration variable matches found lang files');
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

    public function test_js_endpoint_for_each_language()
    {

        $visibleKeys = ['common', 'components', 'entities', 'errors'];

        $this->asEditor();
        foreach ($this->langs as $lang) {
            setting()->putUser($this->getEditor(), 'language', $lang);
            $transResp = $this->get('/translations');
            foreach ($visibleKeys as $key) {
                $transResp->assertSee($key);
            }
        }
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
        $this->assertFalse(config('app.rtl'), "App RTL config should be false by default");
        setting()->putUser($this->getEditor(), 'language', 'ar');
        $this->get('/');
        $this->assertTrue(config('app.rtl'), "App RTL config should have been set to true by middleware");
    }

    public function test_de_informal_falls_base_to_de()
    {
        // Base de back value
        $deBack = trans()->get('common.cancel', [], 'de', false);
        $this->assertEquals('Abbrechen', $deBack);
        // Ensure de_informal has no value set
        $this->assertEquals('common.cancel', trans()->get('common.cancel', [], 'de_informal', false));
        // Ensure standard trans falls back to de
        $this->assertEquals($deBack, trans('common.cancel', [], 'de_informal'));
        // Ensure de_informal gets its own values where set
        $deEmailActionHelp = trans()->get('common.email_action_help', [], 'de', false);
        $enEmailActionHelp = trans()->get('common.email_action_help', [], 'en', false);
        $deInformalEmailActionHelp = trans()->get('common.email_action_help', [], 'de_informal', false);
        $this->assertNotEquals($deEmailActionHelp, $deInformalEmailActionHelp);
        $this->assertNotEquals($enEmailActionHelp, $deInformalEmailActionHelp);
    }

    public function test_de_informal_falls_base_to_de_in_js_endpoint()
    {
        $this->asEditor();
        setting()->putUser($this->getEditor(), 'language', 'de_informal');

        $transResp = $this->get('/translations');
        $transResp->assertSee('"cancel":"Abbrechen"');
    }

}