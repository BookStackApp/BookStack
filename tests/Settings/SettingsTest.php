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
            $this->withHtml($resp)->assertElementContains('h1', $title);
            $this->withHtml($resp)->assertElementExists("form[action$=\"/settings/{$category}\"]");
        }
    }

    public function test_not_found_setting_category_throws_404()
    {
        $resp = $this->asAdmin()->get('/settings/biscuits');

        $resp->assertStatus(404);
        $resp->assertSee('Page Not Found');
    }

    public function test_updating_and_removing_app_icon()
    {
        $this->asAdmin();
        $galleryFile = $this->files->uploadedImage('my-app-icon.png');
        $expectedPath = public_path('uploads/images/system/' . date('Y-m') . '/my-app-icon.png');

        $this->assertFalse(setting()->get('app-icon'));
        $this->assertFalse(setting()->get('app-icon-180'));
        $this->assertFalse(setting()->get('app-icon-128'));
        $this->assertFalse(setting()->get('app-icon-64'));
        $this->assertFalse(setting()->get('app-icon-32'));

        $prevFileCount = count(glob(dirname($expectedPath) . DIRECTORY_SEPARATOR . '*.png'));

        $upload = $this->call('POST', '/settings/customization', [], [], ['app_icon' => $galleryFile], []);
        $upload->assertRedirect('/settings/customization');

        $this->assertTrue(file_exists($expectedPath), 'Uploaded image not found at path: ' . $expectedPath);
        $this->assertStringContainsString('my-app-icon', setting()->get('app-icon'));
        $this->assertStringContainsString('my-app-icon', setting()->get('app-icon-180'));
        $this->assertStringContainsString('my-app-icon', setting()->get('app-icon-128'));
        $this->assertStringContainsString('my-app-icon', setting()->get('app-icon-64'));
        $this->assertStringContainsString('my-app-icon', setting()->get('app-icon-32'));

        $newFileCount = count(glob(dirname($expectedPath) . DIRECTORY_SEPARATOR . '*.png'));
        $this->assertEquals(5, $newFileCount - $prevFileCount);

        $resp = $this->get('/');
        $this->withHtml($resp)->assertElementCount('link[sizes][href*="my-app-icon"]', 6);

        $reset = $this->post('/settings/customization', ['app_icon_reset' => 'true']);
        $reset->assertRedirect('/settings/customization');

        $resetFileCount = count(glob(dirname($expectedPath) . DIRECTORY_SEPARATOR . '*.png'));
        $this->assertEquals($prevFileCount, $resetFileCount);
        $this->assertFalse(setting()->get('app-icon'));
        $this->assertFalse(setting()->get('app-icon-180'));
        $this->assertFalse(setting()->get('app-icon-128'));
        $this->assertFalse(setting()->get('app-icon-64'));
        $this->assertFalse(setting()->get('app-icon-32'));
    }
}
