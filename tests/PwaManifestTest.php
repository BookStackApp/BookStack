<?php

namespace Tests;

class PwaManifestTest extends TestCase
{
    public function test_manifest_access_and_format()
    {
        $this->setSettings(['app-color' => '#00ACED']);

        $resp = $this->get('/manifest.json');
        $resp->assertOk();

        $resp->assertJson([
            'name' => setting('app-name'),
            'launch_handler' => [
                'client_mode' => 'focus-existing'
            ],
            'theme_color' => '#00ACED',
        ]);
    }

    public function test_pwa_meta_tags_in_head()
    {
        $html = $this->asViewer()->withHtml($this->get('/'));

        $html->assertElementExists('head link[rel="manifest"][href$="manifest.json"]');
        $html->assertElementExists('head meta[name="mobile-web-app-capable"][content="yes"]');
    }

    public function test_manifest_uses_configured_icons_if_existing()
    {
        $this->beforeApplicationDestroyed(fn() => $this->files->resetAppFavicon());

        $resp = $this->get('/manifest.json');
        $resp->assertJson([
            'icons' => [[
                "src" => 'http://localhost/icon-32.png',
                "sizes" => "32x32",
                "type" => "image/png"
            ]]
        ]);

        $galleryFile = $this->files->uploadedImage('my-app-icon.png');
        $this->asAdmin()->call('POST', '/settings/customization', [], [], ['app_icon' => $galleryFile], []);

        $customIconUrl = setting()->get('app-icon-32');
        $this->assertStringContainsString('my-app-icon', $customIconUrl);

        $resp = $this->get('/manifest.json');
        $resp->assertJson([
            'icons' => [[
                "src" => $customIconUrl,
                "sizes" => "32x32",
                "type" => "image/png"
            ]]
        ]);
    }

    public function test_manifest_changes_to_user_preferences()
    {
        $lightUser = $this->users->viewer();
        $darkUser = $this->users->editor();
        setting()->putUser($darkUser, 'dark-mode-enabled', 'true');

        $resp = $this->actingAs($lightUser)->get('/manifest.json');
        $resp->assertJson(['background_color' => '#F2F2F2']);

        $resp = $this->actingAs($darkUser)->get('/manifest.json');
        $resp->assertJson(['background_color' => '#111111']);
    }
}
