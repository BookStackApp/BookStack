<?php

namespace Tests\Theme;

use BookStack\Exceptions\ThemeException;
use BookStack\Facades\Theme;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LogicalThemeTest extends TestCase
{
    public function test_theme_functions_file_used_and_app_boot_event_runs()
    {
        $this->usingThemeFolder(function ($themeFolder) {
            $functionsFile = theme_path('functions.php');
            app()->alias('cat', 'dog');
            file_put_contents($functionsFile, "<?php\nTheme::listen(\BookStack\Theming\ThemeEvents::APP_BOOT, function(\$app) { \$app->alias('cat', 'dog');});");
            $this->runWithEnv(['APP_THEME' => $themeFolder], function () {
                $this->assertEquals('cat', $this->app->getAlias('dog'));
            });
        });
    }

    public function test_theme_functions_loads_errors_are_caught_and_logged()
    {
        $this->usingThemeFolder(function ($themeFolder) {
            $functionsFile = theme_path('functions.php');
            file_put_contents($functionsFile, "<?php\n\\BookStack\\Biscuits::eat();");

            $this->expectException(ThemeException::class);
            $this->expectExceptionMessageMatches('/Failed loading theme functions file at ".*?" with error: Class "BookStack\\\\Biscuits" not found/');

            $this->runWithEnv(['APP_THEME' => $themeFolder], fn() => null);
        });
    }

    public function test_add_social_driver()
    {
        Theme::addSocialDriver('catnet', [
            'client_id'     => 'abc123',
            'client_secret' => 'def456',
        ], 'SocialiteProviders\Discord\DiscordExtendSocialite@handleTesting');

        $this->assertEquals('catnet', config('services.catnet.name'));
        $this->assertEquals('abc123', config('services.catnet.client_id'));
        $this->assertEquals(url('/login/service/catnet/callback'), config('services.catnet.redirect'));

        $loginResp = $this->get('/login');
        $loginResp->assertSee('login/service/catnet');
    }

    public function test_add_social_driver_uses_name_in_config_if_given()
    {
        Theme::addSocialDriver('catnet', [
            'client_id'     => 'abc123',
            'client_secret' => 'def456',
            'name'          => 'Super Cat Name',
        ], 'SocialiteProviders\Discord\DiscordExtendSocialite@handleTesting');

        $this->assertEquals('Super Cat Name', config('services.catnet.name'));
        $loginResp = $this->get('/login');
        $loginResp->assertSee('Super Cat Name');
    }

    public function test_add_social_driver_allows_a_configure_for_redirect_callback_to_be_passed()
    {
        Theme::addSocialDriver(
            'discord',
            [
                'client_id'     => 'abc123',
                'client_secret' => 'def456',
                'name'          => 'Super Cat Name',
            ],
            'SocialiteProviders\Discord\DiscordExtendSocialite@handle',
            function ($driver) {
                $driver->with(['donkey' => 'donut']);
            }
        );

        $loginResp = $this->get('/login/service/discord');
        $redirect = $loginResp->headers->get('location');
        $this->assertStringContainsString('donkey=donut', $redirect);
    }

    public function test_register_command_allows_provided_command_to_be_usable_via_artisan()
    {
        Theme::registerCommand(new MyCustomCommand());

        Artisan::call('bookstack:test-custom-command', []);
        $output = Artisan::output();

        $this->assertStringContainsString('Command ran!', $output);
    }
}

class MyCustomCommand extends Command
{
    protected $signature = 'bookstack:test-custom-command';

    public function handle()
    {
        $this->line('Command ran!');
    }
}
