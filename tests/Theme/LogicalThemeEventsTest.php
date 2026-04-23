<?php

namespace Tests\Theme;

use BookStack\Activity\ActivityType;
use BookStack\Activity\DispatchWebhookJob;
use BookStack\Activity\Models\Webhook;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\CommonMark\Environment\Environment;
use Tests\TestCase;

class LogicalThemeEventsTest extends TestCase
{
    public function test_commonmark_environment_configure()
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
        $content->setNewMarkdown('# test', $this->users->editor());

        $this->assertTrue($callbackCalled);
    }

    public function test_web_middleware_before()
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

    public function test_web_middleware_before_return_val_used_as_response()
    {
        $callback = function (Request $request) {
            return response('cat', 412);
        };

        Theme::listen(ThemeEvents::WEB_MIDDLEWARE_BEFORE, $callback);
        $resp = $this->get('/login', ['Donkey' => 'cat']);
        $resp->assertSee('cat');
        $resp->assertStatus(412);
    }

    public function test_web_middleware_after()
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

    public function test_web_middleware_after_return_val_used_as_response()
    {
        $callback = function () {
            return response('cat456', 443);
        };

        Theme::listen(ThemeEvents::WEB_MIDDLEWARE_AFTER, $callback);

        $resp = $this->get('/login', ['Donkey' => 'cat']);
        $resp->assertSee('cat456');
        $resp->assertStatus(443);
    }

    public function test_auth_login_standard()
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

    public function test_auth_register_standard()
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

    public function test_auth_pre_register()
    {
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
        };
        Theme::listen(ThemeEvents::AUTH_PRE_REGISTER, $callback);
        $this->setSettings(['registration-enabled' => 'true']);

        $user = User::factory()->make();
        $this->post('/register', ['email' => $user->email, 'name' => $user->name, 'password' => 'password']);

        $this->assertCount(2, $args);
        $this->assertEquals('standard', $args[0]);
        $this->assertEquals([
            'email' => $user->email,
            'name' => $user->name,
            'password' => 'password',
        ], $args[1]);
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_auth_pre_register_with_false_return_blocks_registration()
    {
        $callback = function () {
            return false;
        };
        Theme::listen(ThemeEvents::AUTH_PRE_REGISTER, $callback);
        $this->setSettings(['registration-enabled' => 'true']);

        $user = User::factory()->make();
        $resp = $this->post('/register', ['email' => $user->email, 'name' => $user->name, 'password' => 'password']);
        $resp->assertRedirect('/login');
        $this->assertSessionError('User account could not be registered for the provided details');
        $this->assertDatabaseMissing('users', ['email' => $user->email]);
    }

    public function test_webhook_call_before()
    {
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;

            return ['test' => 'hello!'];
        };
        Theme::listen(ThemeEvents::WEBHOOK_CALL_BEFORE, $callback);

        $responses = $this->mockHttpClient([new \GuzzleHttp\Psr7\Response(200, [], '')]);

        $webhook = new Webhook(['name' => 'Test webhook', 'endpoint' => 'https://example.com']);
        $webhook->save();
        $event = ActivityType::PAGE_UPDATE;
        $detail = Page::query()->first();

        dispatch((new DispatchWebhookJob($webhook, $event, $detail)));

        $this->assertCount(5, $args);
        $this->assertEquals($event, $args[0]);
        $this->assertEquals($webhook->id, $args[1]->id);
        $this->assertEquals($detail->id, $args[2]->id);

        $this->assertEquals(1, $responses->requestCount());
        $request = $responses->latestRequest();
        $reqData = json_decode($request->getBody(), true);
        $this->assertEquals('hello!', $reqData['test']);
    }

    public function test_activity_logged()
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

    public function test_page_content_pre_store_fires_on_page_save()
    {
        $page = $this->entities->page();

        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
            return '<p>New Content!</p>';
        };

        Theme::listen(ThemeEvents::PAGE_CONTENT_PRE_STORE, $callback);

        $this->asEditor();
        $this->entities->updatePage($page, ['name' => 'My cool update page!', 'html' => '<p>Old content!</p>']);

        $this->assertCount(2, $args);
        $this->assertEquals($page->id, $args[1]->id);
        $this->assertEquals('<p id="bkmrk-old-content%21">Old content!</p>', $args[0]);

        $newPageHtml = $page->refresh()->html;
        $this->assertEquals('<p>New Content!</p>', $newPageHtml);
    }

    public function test_page_content_pre_store_does_not_change_content_if_nothing_returned()
    {
        $page = $this->entities->page();
        Theme::listen(ThemeEvents::PAGE_CONTENT_PRE_STORE, fn() => null);

        $this->asEditor();
        $this->entities->updatePage($page, ['name' => 'My cool update page!', 'html' => '<p>Old content!</p>']);

        $newPageHtml = $page->refresh()->html;
        $this->assertEquals('<p id="bkmrk-old-content%21">Old content!</p>', $newPageHtml);
    }

    public function test_page_content_post_render_fires_on_page_view()
    {
        $page = $this->entities->page();
        $page->html = '<p>Old content!</p>';
        $page->save();

        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
            return '<p>New postrendercontentforyou!</p>';
        };

        Theme::listen(ThemeEvents::PAGE_CONTENT_POST_RENDER, $callback);

        $resp = $this->asEditor()->get($page->getUrl());
        $resp->assertSee('<p>New postrendercontentforyou!</p>', false);

        $this->assertCount(2, $args);
        $this->assertEquals($page->id, $args[1]->id);
        $this->assertEquals('<p>Old content!</p>', $args[0]);
    }

    public function test_page_content_post_render_returns_original_content_if_no_return()
    {
        $page = $this->entities->page();
        $page->html = '<p>Old content!</p>';
        $page->save();

        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
        };

        Theme::listen(ThemeEvents::PAGE_CONTENT_POST_RENDER, $callback);

        $resp = $this->asEditor()->get($page->getUrl());
        $resp->assertSee('<p>Old content!</p>', false);

        $this->assertCount(2, $args);
    }

    public function test_page_include_parse()
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

    public function test_routes_register_web_and_web_auth()
    {
        $functionsContent = <<<'END'
<?php
use BookStack\Theming\ThemeEvents;
use BookStack\Facades\Theme;
use Illuminate\Routing\Router;
Theme::listen(ThemeEvents::ROUTES_REGISTER_WEB, function (Router $router) {
    $router->get('/cat', fn () => 'cat')->name('say.cat');
});
Theme::listen(ThemeEvents::ROUTES_REGISTER_WEB_AUTH, function (Router $router) {
    $router->get('/dog', fn () => 'dog')->name('say.dog');
});
END;

        $this->usingThemeFolder(function () use ($functionsContent) {

            $functionsFile = theme_path('functions.php');
            file_put_contents($functionsFile, $functionsContent);

            $app = $this->createApplication();
            /** @var \Illuminate\Routing\Router $router */
            $router = $app->get('router');

            /** @var \Illuminate\Routing\Route $catRoute */
            $catRoute = $router->getRoutes()->getRoutesByName()['say.cat'];
            $this->assertEquals(['web'], $catRoute->middleware());

            /** @var \Illuminate\Routing\Route $dogRoute */
            $dogRoute = $router->getRoutes()->getRoutesByName()['say.dog'];
            $this->assertEquals(['web', 'auth'], $dogRoute->middleware());
        });
    }

    public function test_register_views_to_insert_views_before_and_after()
    {
        $this->usingThemeFolder(function (string $folder) {
            $before = 'this-is-my-before-header-string';
            $afterA = 'this-is-my-after-header-string-a';
            $afterB = 'this-is-my-after-header-string-b';
            $afterC = 'this-is-my-after-header-string-{{ 1+51 }}';

            $functionsContent = <<<'CONTENT'
<?php use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Theming\ThemeViews;
Theme::listen(ThemeEvents::THEME_REGISTER_VIEWS, function (ThemeViews $themeViews) {
    $themeViews->renderBefore('layouts.parts.header', 'before', 4);
    $themeViews->renderAfter('layouts.parts.header', 'after-a', 4);
    $themeViews->renderAfter('layouts.parts.header', 'after-b', 1);
    $themeViews->renderAfter('layouts.parts.header', 'after-c', 12);
});
CONTENT;

            $viewDir = theme_path();
            file_put_contents($viewDir . '/functions.php', $functionsContent);
            file_put_contents($viewDir . '/before.blade.php', $before);
            file_put_contents($viewDir . '/after-a.blade.php', $afterA);
            file_put_contents($viewDir . '/after-b.blade.php', $afterB);
            file_put_contents($viewDir . '/after-c.blade.php', $afterC);

            $this->refreshApplication();
            $this->artisan('view:clear');

            $resp = $this->get('/login');
            $resp->assertSee($before);
            // Ensure ordering of the multiple after views
            $resp->assertSee($afterB . "\n" . $afterA . "\nthis-is-my-after-header-string-52");
        });

        $this->artisan('view:clear');
    }
}
