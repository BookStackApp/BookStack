<?php namespace Tests;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use File;
use League\CommonMark\ConfigurableEnvironmentInterface;

class ThemeTest extends TestCase
{
    protected $themeFolderName;
    protected $themeFolderPath;

    public function test_translation_text_can_be_overridden_via_theme()
    {
        $this->usingThemeFolder(function() {
            $translationPath = theme_path('/lang/en');
            File::makeDirectory($translationPath, 0777, true);

            $customTranslations = '<?php
            return [\'books\' => \'Sandwiches\'];
        ';
            file_put_contents($translationPath . '/entities.php', $customTranslations);

            $homeRequest = $this->actingAs($this->getViewer())->get('/');
            $homeRequest->assertElementContains('header nav', 'Sandwiches');
        });
    }

    public function test_theme_functions_file_used_and_app_boot_event_runs()
    {
        $this->usingThemeFolder(function($themeFolder) {
            $functionsFile = theme_path('functions.php');
            app()->alias('cat', 'dog');
            file_put_contents($functionsFile, "<?php\nTheme::listen(\BookStack\Theming\ThemeEvents::APP_BOOT, function(\$app) { \$app->alias('cat', 'dog');});");
            $this->runWithEnv('APP_THEME', $themeFolder, function() {
                $this->assertEquals('cat', $this->app->getAlias('dog'));
            });
        });
    }

    public function test_event_commonmark_environment_configure()
    {
        $callbackCalled = false;
        $callback = function($environment) use (&$callbackCalled) {
            $this->assertInstanceOf(ConfigurableEnvironmentInterface::class, $environment);
            $callbackCalled = true;
            return $environment;
        };
        Theme::listen(ThemeEvents::COMMONMARK_ENVIRONMENT_CONFIGURE, $callback);

        $page = Page::query()->first();
        $content = new PageContent($page);
        $content->setNewMarkdown('# test');

        $this->assertTrue($callbackCalled);
    }

    protected function usingThemeFolder(callable $callback)
    {
        // Create a folder and configure a theme
        $themeFolderName = 'testing_theme_' . rtrim(base64_encode(time()), "=");
        config()->set('view.theme', $themeFolderName);
        $themeFolderPath = theme_path('');
        File::makeDirectory($themeFolderPath);

        call_user_func($callback, $themeFolderName);

        // Cleanup the custom theme folder we created
        File::deleteDirectory($themeFolderPath);
    }

}