<?php

namespace Tests\Theme;

use BookStack\Facades\Theme;
use BookStack\Util\CspService;
use Tests\TestCase;

class ThemeModuleTest extends TestCase
{
    public function test_modules_loaded_on_theme_load()
    {
        $this->usingThemeFolder(function ($themeFolder) {
            $a = theme_path('modules/a');
            $b = theme_path('modules/b');
            mkdir($a, 0777, true);
            mkdir($b, 0777, true);

            file_put_contents($a . '/bookstack-module.json', json_encode([
                'name' => 'Module A',
                'description' => 'This is module A',
                'version' => '1.0.0',
            ]));
            file_put_contents($b . '/bookstack-module.json', json_encode([
                'name' => 'Module B',
                'description' => 'This is module B',
                'version' => 'v0.5.0',
            ]));

            $this->refreshApplication();

            $modules = Theme::getModules();
            $this->assertCount(2, $modules);

            $moduleA = $modules['a'];
            $this->assertEquals('Module A', $moduleA->name);
            $this->assertEquals('This is module A', $moduleA->description);
            $this->assertEquals('1.0.0', $moduleA->version);
        });
    }

    public function test_module_not_loaded_if_no_bookstack_module_json()
    {
        $this->usingThemeFolder(function ($themeFolder) {
            $moduleDir = theme_path('/modules/a');
            mkdir($moduleDir, 0777, true);
            file_put_contents($moduleDir . '/module.json', '{}');
            $this->refreshApplication();
            $modules = Theme::getModules();
            $this->assertCount(0, $modules);
        });
    }

    public function test_language_text_overridable_via_module()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $translationPath = $moduleFolderPath . '/lang/en';
            mkdir($translationPath, 0777, true);
            file_put_contents($translationPath . '/entities.php', '<?php return ["books" => "SuperBeans"];');
            $this->refreshApplication();

            $this->asAdmin()->get('/books')->assertSee('SuperBeans');
        });
    }

    public function test_language_files_merge_with_theme_files_with_theme_taking_precedence()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $moduleTranslationPath = $moduleFolderPath . '/lang/en';
            mkdir($moduleTranslationPath, 0777, true);
            file_put_contents($moduleTranslationPath . '/entities.php', '<?php return ["books" => "SuperBeans", "recently_viewed" => "ViewedBiscuits"];');

            $themeTranslationPath = theme_path('lang/en');
            mkdir($themeTranslationPath, 0777, true);
            file_put_contents($themeTranslationPath . '/entities.php', '<?php return ["books" => "WonderBeans"];');
            $this->refreshApplication();

            $this->asAdmin()->get('/books')
                ->assertSee('WonderBeans')
                ->assertDontSee('SuperBeans')
                ->assertSee('ViewedBiscuits');
        });
    }

    public function test_view_files_overridable_from_module()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $viewsFolder = $moduleFolderPath . '/views/layouts/parts';
            mkdir($viewsFolder, 0777, true);
            file_put_contents($viewsFolder . '/header.blade.php', 'My custom header that says badgerriffic');
            $this->refreshApplication();
            $this->asAdmin()->get('/')->assertSee('badgerriffic');
        });
    }

    public function test_theme_view_files_take_precedence_over_module_view_files()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $viewsFolder = $moduleFolderPath . '/views/layouts/parts';
            mkdir($viewsFolder, 0777, true);
            file_put_contents($viewsFolder . '/header.blade.php', 'My custom header that says badgerriffic');

            $themeViewsFolder = theme_path('layouts/parts');
            mkdir($themeViewsFolder, 0777, true);
            file_put_contents($themeViewsFolder . '/header.blade.php', 'My theme header that says awesomeferrets');

            $this->refreshApplication();
            $this->asAdmin()->get('/')
                ->assertDontSee('badgerriffic')
                ->assertSee('awesomeferrets');
        });
    }

    public function test_theme_and_modules_views_can_be_used_at_the_same_time()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $viewsFolder = $moduleFolderPath . '/views/layouts/parts';
            mkdir($viewsFolder, 0777, true);
            file_put_contents($viewsFolder . '/base-body-start.blade.php', 'My custom header that says badgerriffic');

            $themeViewsFolder = theme_path('layouts/parts');
            mkdir($themeViewsFolder, 0777, true);
            file_put_contents($themeViewsFolder . '/base-body-end.blade.php', 'My theme header that says awesomeferrets');

            $this->refreshApplication();
            $this->asAdmin()->get('/')
                ->assertSee('badgerriffic')
                ->assertSee('awesomeferrets');
        });
    }

    public function test_icons_can_be_overridden_from_module()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $iconsFolder = $moduleFolderPath . '/icons';
            mkdir($iconsFolder, 0777, true);
            file_put_contents($iconsFolder . '/books.svg', '<svg><path d="supericonpath"/></svg>');
            $this->refreshApplication();

            $this->asAdmin()->get('/')->assertSee('supericonpath', false);
        });
    }

    public function test_theme_icons_take_precedence_over_module_icons()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $iconsFolder = $moduleFolderPath . '/icons';
            mkdir($iconsFolder, 0777, true);
            file_put_contents($iconsFolder . '/books.svg', '<svg><path d="supericonpath"/></svg>');
            $this->refreshApplication();

            $themeViewsFolder = theme_path('icons');
            mkdir($themeViewsFolder, 0777, true);
            file_put_contents($themeViewsFolder . '/books.svg', '<svg><path d="wackyiconpath"/></svg>');


            $this->asAdmin()->get('/')
                ->assertSee('wackyiconpath', false)
                ->assertDontSee('supericonpath', false);
        });
    }

    public function test_public_folder_can_be_provided_from_module()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $publicFolder = $moduleFolderPath . '/public';
            mkdir($publicFolder, 0777, true);
            $themeName = basename(dirname(dirname($moduleFolderPath)));
            file_put_contents($publicFolder . '/test.txt', 'hellofrominsidethisfileimaghostwoooo!');
            $this->refreshApplication();

            $resp = $this->asAdmin()->get("/theme/{$themeName}/test.txt")->streamedContent();
            $this->assertEquals('hellofrominsidethisfileimaghostwoooo!', $resp);
        });
    }

    public function test_theme_public_files_take_precedence_over_modules()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            $publicFolder = $moduleFolderPath . '/public';
            mkdir($publicFolder, 0777, true);
            $themeName = basename(theme_path());
            file_put_contents($publicFolder . '/test.txt', 'hellofrominsidethisfileimaghostwoooo!');

            $themePublicFolder = theme_path('public');
            mkdir($themePublicFolder, 0777, true);
            file_put_contents($themePublicFolder . '/test.txt', 'imadifferentghostinsidethetheme,woooooo!');

            $this->refreshApplication();

            $resp = $this->asAdmin()->get("/theme/{$themeName}/test.txt")->streamedContent();
            $this->assertEquals('imadifferentghostinsidethetheme,woooooo!', $resp);
        });
    }

    public function test_logical_functions_file_loaded_from_module_and_it_runs_alongside_theme_functions()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            file_put_contents($moduleFolderPath . '/functions.php', "<?php\nTheme::listen(\BookStack\Theming\ThemeEvents::APP_BOOT, function(\$app) { \$app->alias('cat', 'dog');});");

            $themeFunctionsFile = theme_path('functions.php');
            file_put_contents($themeFunctionsFile, "<?php\nTheme::listen(\BookStack\Theming\ThemeEvents::APP_BOOT, function(\$app) { \$app->alias('beans', 'cheese');});");

            $this->refreshApplication();

            $this->assertEquals('cat', $this->app->getAlias('dog'));
            $this->assertEquals('beans', $this->app->getAlias('cheese'));
        });
    }

    public function test_module_can_use_theme_view_render_functions()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            file_put_contents($moduleFolderPath . '/functions.php', "<?php\n\BookStack\Facades\Theme::listen(\BookStack\Theming\ThemeEvents::THEME_REGISTER_VIEWS, fn(\$views) => \$views->renderBefore('layouts.parts.header', 'cat', 100));");
            mkdir($moduleFolderPath . '/views', 0777, true);
            file_put_contents($moduleFolderPath . '/views/cat.blade.php', 'mysupercatispouncy');

            $this->refreshApplication();

            $this->asAdmin()->get('/')->assertSee('mysupercatispouncy');
        });
    }

    public function test_module_can_provide_head_content()
    {
        $this->usingModuleFolder(function (string $moduleFolderPath) {
            mkdir($moduleFolderPath . '/head', 0777, true);
            file_put_contents($moduleFolderPath . '/head/hello.html', '<meta name="beans" content="hello"><script>hellofromcustomscript</script>');

            $this->refreshApplication();

            $cspService = $this->app->make(CspService::class);
            $nonce = $cspService->getNonce();

            $resp = $this->asAdmin()->get('/');
            $resp->assertSee('<meta name="beans" content="hello">', false);
            $resp->assertSee('<script nonce="' . $nonce . '">hellofromcustomscript</script>', false);
        });
    }

    protected function usingModuleFolder(callable $callback): void
    {
        $this->usingThemeFolder(function (string $themeFolder) use ($callback) {
            $moduleFolderPath = theme_path('modules/test-module');
            mkdir($moduleFolderPath, 0777, true);
            file_put_contents($moduleFolderPath . '/bookstack-module.json', json_encode([
                'name' => 'Test Module',
                'description' => 'This is a test module',
                'version' => 'v1.0.0',
            ]));
            $callback($moduleFolderPath);
        });
    }
}
