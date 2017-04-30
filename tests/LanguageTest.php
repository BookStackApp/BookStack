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
        $this->langs = array_diff(scandir(resource_path('lang')), ['..', '.']);
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

}