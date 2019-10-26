<?php namespace Tests;

use File;

class ThemeTest extends TestCase
{
    protected $themeFolderName;
    protected $themeFolderPath;

    public function setUp(): void
    {
        parent::setUp();

        // Create a folder and configure a theme
        $this->themeFolderName = 'testing_theme_' . rtrim(base64_encode(time()), "=");
        config()->set('view.theme', $this->themeFolderName);
        $this->themeFolderPath = theme_path('');
        File::makeDirectory($this->themeFolderPath);
    }

    public function tearDown(): void
    {
        // Cleanup the custom theme folder we created
        File::deleteDirectory($this->themeFolderPath);

        parent::tearDown();
    }

    public function test_translation_text_can_be_overriden_via_theme()
    {
        $translationPath = theme_path('/lang/en');
        File::makeDirectory($translationPath, 0777, true);

        $customTranslations = '<?php
            return [\'books\' => \'Sandwiches\'];
        ';
        file_put_contents($translationPath . '/entities.php', $customTranslations);

        $homeRequest = $this->actingAs($this->getViewer())->get('/');
        $homeRequest->assertElementContains('header nav', 'Sandwiches');
    }

}