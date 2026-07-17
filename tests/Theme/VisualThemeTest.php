<?php

namespace Tests\Theme;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class VisualThemeTest extends TestCase
{
    public function test_translation_text_can_be_overridden_via_theme()
    {
        $this->usingThemeFolder(function () {
            $translationPath = theme_path('/lang/en');
            File::makeDirectory($translationPath, 0777, true);

            $customTranslations = '<?php
            return [\'books\' => \'Sandwiches\'];
        ';
            file_put_contents($translationPath . '/entities.php', $customTranslations);

            $homeRequest = $this->actingAs($this->users->viewer())->get('/');
            $this->withHtml($homeRequest)->assertElementContains('header nav', 'Sandwiches');
        });
    }

    public function test_custom_settings_category_page_can_be_added_via_view_file()
    {
        $content = 'My SuperCustomSettings';

        $this->usingThemeFolder(function (string $folder) use ($content) {
            $viewDir = theme_path('settings/categories');
            mkdir($viewDir, 0777, true);
            file_put_contents($viewDir . '/beans.blade.php', $content);

            $this->asAdmin()->get('/settings/beans')->assertSee($content);
        });
    }

    public function test_base_body_start_and_end_template_files_can_be_used()
    {
        $bodyStartStr = 'barry-fought-against-the-panther';
        $bodyEndStr = 'barry-lost-his-fight-with-grace';

        $this->usingThemeFolder(function (string $folder) use ($bodyStartStr, $bodyEndStr) {
            $viewDir = theme_path('layouts/parts');
            mkdir($viewDir, 0777, true);
            file_put_contents($viewDir . '/base-body-start.blade.php', $bodyStartStr);
            file_put_contents($viewDir . '/base-body-end.blade.php', $bodyEndStr);

            $resp = $this->asEditor()->get('/');
            $resp->assertSee($bodyStartStr);
            $resp->assertSee($bodyEndStr);
        });
    }

    public function test_export_body_start_and_end_template_files_can_be_used()
    {
        $bodyStartStr = 'garry-fought-against-the-panther';
        $bodyEndStr = 'garry-lost-his-fight-with-grace';
        $page = $this->entities->page();

        $this->usingThemeFolder(function (string $folder) use ($bodyStartStr, $bodyEndStr, $page) {
            $viewDir = theme_path('layouts/parts');
            mkdir($viewDir, 0777, true);
            file_put_contents($viewDir . '/export-body-start.blade.php', $bodyStartStr);
            file_put_contents($viewDir . '/export-body-end.blade.php', $bodyEndStr);

            $resp = $this->asEditor()->get($page->getUrl('/export/html'));
            $resp->assertSee($bodyStartStr);
            $resp->assertSee($bodyEndStr);
        });
    }

    public function test_login_and_register_message_template_files_can_be_used()
    {
        $loginMessage = 'Welcome to this instance, login below you scallywag';
        $registerMessage = 'You want to register? Enter the deets below you numpty';

        $this->usingThemeFolder(function (string $folder) use ($loginMessage, $registerMessage) {
            $viewDir = theme_path('auth/parts');
            mkdir($viewDir, 0777, true);
            file_put_contents($viewDir . '/login-message.blade.php', $loginMessage);
            file_put_contents($viewDir . '/register-message.blade.php', $registerMessage);
            $this->setSettings(['registration-enabled' => 'true']);

            $this->get('/login')->assertSee($loginMessage);
            $this->get('/register')->assertSee($registerMessage);
        });
    }

    public function test_header_links_start_template_file_can_be_used()
    {
        $content = 'This is added text in the header bar';

        $this->usingThemeFolder(function (string $folder) use ($content) {
            $viewDir = theme_path('layouts/parts');
            mkdir($viewDir, 0777, true);
            file_put_contents($viewDir . '/header-links-start.blade.php', $content);
            $this->setSettings(['registration-enabled' => 'true']);

            $this->get('/login')->assertSee($content);
        });
    }

    public function test_public_folder_contents_accessible_via_route()
    {
        $this->usingThemeFolder(function (string $themeFolderName) {
            $publicDir = theme_path('public');
            mkdir($publicDir, 0777, true);

            $text = 'some-text ' . md5(random_bytes(5));
            $css = "body { background-color: tomato !important; }";
            file_put_contents("{$publicDir}/file.txt", $text);
            file_put_contents("{$publicDir}/file.css", $css);
            copy($this->files->testFilePath('test-image.png'), "{$publicDir}/image.png");

            $resp = $this->asAdmin()->get("/theme/{$themeFolderName}/file.txt");
            $resp->assertStreamedContent($text);
            $resp->assertHeader('Content-Type', 'text/plain; charset=utf-8');
            $resp->assertHeader('Cache-Control', 'max-age=86400, private');

            $resp = $this->asAdmin()->get("/theme/{$themeFolderName}/image.png");
            $resp->assertHeader('Content-Type', 'image/png');
            $resp->assertHeader('Cache-Control', 'max-age=86400, private');

            $resp = $this->asAdmin()->get("/theme/{$themeFolderName}/file.css");
            $resp->assertStreamedContent($css);
            $resp->assertHeader('Content-Type', 'text/css; charset=utf-8');
            $resp->assertHeader('Cache-Control', 'max-age=86400, private');
        });
    }
}
