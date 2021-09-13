<?php

namespace Tests;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Settings\SettingService;
use DB;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\BrowserKitTesting\TestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BrowserKitTest extends TestCase
{
    use DatabaseTransactions;
    use SharedTestHelpers;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public function tearDown(): void
    {
        DB::disconnect();
        parent::tearDown();
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Quickly sets an array of settings.
     *
     * @param $settingsArray
     */
    protected function setSettings($settingsArray)
    {
        $settings = app(SettingService::class);
        foreach ($settingsArray as $key => $value) {
            $settings->put($key, $value);
        }
    }

    /**
     * Helper for updating entity permissions.
     *
     * @param Entity $entity
     */
    protected function updateEntityPermissions(Entity $entity)
    {
        $restrictionService = $this->app[PermissionService::class];
        $restrictionService->buildJointPermissionsForEntity($entity);
    }

    /**
     * Assert that a given string is seen inside an element.
     *
     * @param bool|string|null $element
     * @param int              $position
     * @param string           $text
     * @param bool             $negate
     *
     * @return $this
     */
    protected function seeInNthElement($element, $position, $text, $negate = false)
    {
        $method = $negate ? 'assertDoesNotMatchRegularExpression' : 'assertMatchesRegularExpression';

        $rawPattern = preg_quote($text, '/');

        $escapedPattern = preg_quote(e($text), '/');

        $content = $this->crawler->filter($element)->eq($position)->html();

        $pattern = $rawPattern == $escapedPattern
            ? $rawPattern : "({$rawPattern}|{$escapedPattern})";

        $this->$method("/$pattern/i", $content);

        return $this;
    }

    /**
     * Assert that the current page matches a given URI.
     *
     * @param string $uri
     *
     * @return $this
     */
    protected function seePageUrlIs($uri)
    {
        $this->assertEquals(
            $uri,
            $this->currentUri,
            "Did not land on expected page [{$uri}].\n"
        );

        return $this;
    }

    /**
     * Do a forced visit that does not error out on exception.
     *
     * @param string $uri
     * @param array  $parameters
     * @param array  $cookies
     * @param array  $files
     *
     * @return $this
     */
    protected function forceVisit($uri, $parameters = [], $cookies = [], $files = [])
    {
        $method = 'GET';
        $uri = $this->prepareUrlForRequest($uri);
        $this->call($method, $uri, $parameters, $cookies, $files);
        $this->clearInputs()->followRedirects();
        $this->currentUri = $this->app->make('request')->fullUrl();
        $this->crawler = new Crawler($this->response->getContent(), $uri);

        return $this;
    }

    /**
     * Click the text within the selected element.
     *
     * @param $parentElement
     * @param $linkText
     *
     * @return $this
     */
    protected function clickInElement($parentElement, $linkText)
    {
        $elem = $this->crawler->filter($parentElement);
        $link = $elem->selectLink($linkText);
        $this->visit($link->link()->getUri());

        return $this;
    }

    /**
     * Check if the page contains the given element.
     *
     * @param string $selector
     */
    protected function pageHasElement($selector)
    {
        $elements = $this->crawler->filter($selector);
        $this->assertTrue(count($elements) > 0, 'The page does not contain an element matching ' . $selector);

        return $this;
    }

    /**
     * Check if the page contains the given element.
     *
     * @param string $selector
     */
    protected function pageNotHasElement($selector)
    {
        $elements = $this->crawler->filter($selector);
        $this->assertFalse(count($elements) > 0, 'The page contains ' . count($elements) . ' elements matching ' . $selector);

        return $this;
    }
}
