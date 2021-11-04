<?php

namespace Tests\Settings;

use Tests\TestCase;

class FooterLinksTest extends TestCase
{
    public function test_saving_setting()
    {
        $resp = $this->asAdmin()->post('/settings', [
            'setting-app-footer-links' => [
                ['label' => 'My custom link 1', 'url' => 'https://example.com/1'],
                ['label' => 'My custom link 2', 'url' => 'https://example.com/2'],
            ],
        ]);
        $resp->assertRedirect('/settings');

        $result = setting('app-footer-links');
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('My custom link 2', $result[1]['label']);
        $this->assertEquals('https://example.com/1', $result[0]['url']);
    }

    public function test_set_options_visible_on_settings_page()
    {
        $this->setSettings(['app-footer-links' => [
            ['label' => 'My custom link', 'url' => 'https://example.com/link-a'],
            ['label' => 'Another Link', 'url' => 'https://example.com/link-b'],
        ]]);

        $resp = $this->asAdmin()->get('/settings');
        $resp->assertSee('value="My custom link"', false);
        $resp->assertSee('value="Another Link"', false);
        $resp->assertSee('value="https://example.com/link-a"', false);
        $resp->assertSee('value="https://example.com/link-b"', false);
    }

    public function test_footer_links_show_on_pages()
    {
        $this->setSettings(['app-footer-links' => [
            ['label' => 'My custom link', 'url' => 'https://example.com/link-a'],
            ['label' => 'Another Link', 'url' => 'https://example.com/link-b'],
        ]]);

        $this->get('/login')->assertElementContains('footer a[href="https://example.com/link-a"]', 'My custom link');
        $this->asEditor()->get('/')->assertElementContains('footer a[href="https://example.com/link-b"]', 'Another link');
    }

    public function test_using_translation_system_for_labels()
    {
        $this->setSettings(['app-footer-links' => [
            ['label' => 'trans::common.privacy_policy', 'url' => 'https://example.com/privacy'],
            ['label' => 'trans::common.terms_of_service', 'url' => 'https://example.com/terms'],
        ]]);

        $resp = $this->get('/login');
        $resp->assertElementContains('footer a[href="https://example.com/privacy"]', 'Privacy Policy');
        $resp->assertElementContains('footer a[href="https://example.com/terms"]', 'Terms of Service');
    }
}
