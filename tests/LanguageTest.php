<?php namespace Tests;

class LanguageTest extends TestCase
{

    public function test_js_endpoint_for_each_language() {

        $langs = array_diff(scandir(resource_path('lang')), ['..', '.']);
        $visibleKeys = ['common', 'components', 'entities', 'errors'];

        $this->asEditor();
        foreach ($langs as $lang) {
            setting()->putUser($this->getEditor(), 'language', $lang);
            $transResp = $this->get('/translations');
            foreach ($visibleKeys as $key) {
                $transResp->assertSee($key);
            }
        }
    }

}