<?php

namespace Tests;

use BookStack\Actions\ActivityType;
use BookStack\Actions\DispatchWebhookJob;
use BookStack\Actions\Webhook;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Request as HttpClientRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Environment\Environment;

class ThemeTest extends TestCase
{
    protected string $themeFolderName;
    protected string $themeFolderPath;

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

    public function test_theme_functions_file_used_and_app_boot_event_runs()
    {
        $this->usingThemeFolder(function ($themeFolder) {
            $functionsFile = theme_path('functions.php');
            app()->alias('cat', 'dog');
            file_put_contents($functionsFile, "<?php\nTheme::listen(\BookStack\Theming\ThemeEvents::APP_BOOT, function(\$app) { \$app->alias('cat', 'dog');});");
            $this->runWithEnv('APP_THEME', $themeFolder, function () {
                $this->assertEquals('cat', $this->app->getAlias('dog'));
            });
        });
    }

    public function test_event_commonmark_environment_configure()
    {
        $callbackCalled = false;
        $callback = function ($environment) use (&$callbackCalled) {
            $this->assertInstanceOf(Environment::class, $environment);
            $callbackCalled = true;

            return $environment;
        };
        Theme::listen(ThemeEvents::COMMONMARK_ENVIRONMENT_CONFIGURE, $callback);

        $page = $this->entities->page();
        $content = new PageContent($page);
        $content->setNewMarkdown('# test');

        $this->assertTrue($callbackCalled);
    }

    public function test_event_web_middleware_before()
    {
        $callbackCalled = false;
        $requestParam = null;
        $callback = function ($request) use (&$callbackCalled, &$requestParam) {
            $requestParam = $request;
            $callbackCalled = true;
        };

        Theme::listen(ThemeEvents::WEB_MIDDLEWARE_BEFORE, $callback);
        $this->get('/login', ['Donkey' => 'cat']);

        $this->assertTrue($callbackCalled);
        $this->assertInstanceOf(Request::class, $requestParam);
        $this->assertEquals('cat', $requestParam->header('donkey'));
    }

    public function test_event_web_middleware_before_return_val_used_as_response()
    {
        $callback = function (Request $request) {
            return response('cat', 412);
        };

        Theme::listen(ThemeEvents::WEB_MIDDLEWARE_BEFORE, $callback);
        $resp = $this->get('/login', ['Donkey' => 'cat']);
        $resp->assertSee('cat');
        $resp->assertStatus(412);
    }

    public function test_event_web_middleware_after()
    {
        $callbackCalled = false;
        $requestParam = null;
        $responseParam = null;
        $callback = function ($request, Response $response) use (&$callbackCalled, &$requestParam, &$responseParam) {
            $requestParam = $request;
            $responseParam = $response;
            $callbackCalled = true;
            $response->header('donkey', 'cat123');
        };

        Theme::listen(ThemeEvents::WEB_MIDDLEWARE_AFTER, $callback);

        $resp = $this->get('/login', ['Donkey' => 'cat']);
        $this->assertTrue($callbackCalled);
        $this->assertInstanceOf(Request::class, $requestParam);
        $this->assertInstanceOf(Response::class, $responseParam);
        $resp->assertHeader('donkey', 'cat123');
    }

    public function test_event_web_middleware_after_return_val_used_as_response()
    {
        $callback = function () {
            return response('cat456', 443);
        };

        Theme::listen(ThemeEvents::WEB_MIDDLEWARE_AFTER, $callback);

        $resp = $this->get('/login', ['Donkey' => 'cat']);
        $resp->assertSee('cat456');
        $resp->assertStatus(443);
    }

    public function test_event_auth_login_standard()
    {
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
        };

        Theme::listen(ThemeEvents::AUTH_LOGIN, $callback);
        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);

        $this->assertCount(2, $args);
        $this->assertEquals('standard', $args[0]);
        $this->assertInstanceOf(User::class, $args[1]);
    }

    public function test_event_auth_register_standard()
    {
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
        };
        Theme::listen(ThemeEvents::AUTH_REGISTER, $callback);
        $this->setSettings(['registration-enabled' => 'true']);

        $user = User::factory()->make();
        $this->post('/register', ['email' => $user->email, 'name' => $user->name, 'password' => 'password']);

        $this->assertCount(2, $args);
        $this->assertEquals('standard', $args[0]);
        $this->assertInstanceOf(User::class, $args[1]);
    }

    public function test_event_webhook_call_before()
    {
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;

            return ['test' => 'hello!'];
        };
        Theme::listen(ThemeEvents::WEBHOOK_CALL_BEFORE, $callback);

        Http::fake([
            '*' => Http::response('', 200),
        ]);

        $webhook = new Webhook(['name' => 'Test webhook', 'endpoint' => 'https://example.com']);
        $webhook->save();
        $event = ActivityType::PAGE_UPDATE;
        $detail = Page::query()->first();

        dispatch((new DispatchWebhookJob($webhook, $event, $detail)));

        $this->assertCount(5, $args);
        $this->assertEquals($event, $args[0]);
        $this->assertEquals($webhook->id, $args[1]->id);
        $this->assertEquals($detail->id, $args[2]->id);

        Http::assertSent(function (HttpClientRequest $request) {
            return $request->isJson() && $request->data()['test'] === 'hello!';
        });
    }

    public function test_event_activity_logged()
    {
        $book = $this->entities->book();
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
        };

        Theme::listen(ThemeEvents::ACTIVITY_LOGGED, $callback);
        $this->asEditor()->put($book->getUrl(), ['name' => 'My cool update book!']);

        $this->assertCount(2, $args);
        $this->assertEquals(ActivityType::BOOK_UPDATE, $args[0]);
        $this->assertTrue($args[1] instanceof Book);
        $this->assertEquals($book->id, $args[1]->id);
    }

    public function test_event_page_include_parse()
    {
        /** @var Page $page */
        /** @var Page $otherPage */
        $page = $this->entities->page();
        $otherPage = Page::query()->where('id', '!=', $page->id)->first();
        $otherPage->html = '<p id="bkmrk-cool">This is a really cool section</p>';
        $page->html = "<p>{{@{$otherPage->id}#bkmrk-cool}}</p>";
        $page->save();
        $otherPage->save();

        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;

            return '<strong>Big &amp; content replace surprise!</strong>';
        };

        Theme::listen(ThemeEvents::PAGE_INCLUDE_PARSE, $callback);
        $resp = $this->asEditor()->get($page->getUrl());
        $this->withHtml($resp)->assertElementContains('.page-content strong', 'Big & content replace surprise!');

        $this->assertCount(4, $args);
        $this->assertEquals($otherPage->id . '#bkmrk-cool', $args[0]);
        $this->assertEquals('This is a really cool section', $args[1]);
        $this->assertTrue($args[2] instanceof Page);
        $this->assertTrue($args[3] instanceof Page);
        $this->assertEquals($page->id, $args[2]->id);
        $this->assertEquals($otherPage->id, $args[3]->id);
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

    protected function usingThemeFolder(callable $callback)
    {
        // Create a folder and configure a theme
        $themeFolderName = 'testing_theme_' . str_shuffle(rtrim(base64_encode(time()), '='));
        config()->set('view.theme', $themeFolderName);
        $themeFolderPath = theme_path('');

        // Create theme folder and clean it up on application tear-down
        File::makeDirectory($themeFolderPath);
        $this->beforeApplicationDestroyed(fn() => File::deleteDirectory($themeFolderPath));

        // Run provided callback with theme env option set
        $this->runWithEnv('APP_THEME', $themeFolderName, function () use ($callback, $themeFolderName) {
            call_user_func($callback, $themeFolderName);
        });
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
