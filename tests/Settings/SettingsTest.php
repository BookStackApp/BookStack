<?php

namespace Tests\Settings;

use Tests\TestCase;

class SettingsTest extends TestCase
{
    public function test_settings_endpoint_redirects_to_settings_view()
    {
        $resp = $this->asAdmin()->get('/settings');

        $resp->assertStatus(302);

        // Manually check path to ensure it's generated as the full path
        $location = $resp->headers->get('location');
        $this->assertEquals(url('/settings/features'), $location);
    }

    public function test_settings_category_links_work_as_expected()
    {
        $this->asAdmin();
        $categories = [
            'features'      => 'Features & Security',
            'customization' => 'Customization',
            'registration'  => 'Registration',
        ];

        foreach ($categories as $category => $title) {
            $resp = $this->get("/settings/{$category}");
            $resp->assertElementContains('h1', $title);
            $resp->assertElementExists("form[action$=\"/settings/{$category}\"]");
        }
    }

    public function test_not_found_setting_category_throws_404()
    {
        $resp = $this->asAdmin()->get('/settings/biscuits');

        $resp->assertStatus(404);
        $resp->assertSee('Page Not Found');
    }
}
