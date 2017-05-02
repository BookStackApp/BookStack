<?php namespace Tests;

use BookStack\Role;
use BookStack\Services\PermissionService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\BrowserKitTesting\TestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BrowserKitTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    // Local user instances
    private $admin;
    private $editor;

    public function tearDown()
    {
        \DB::disconnect();
        parent::tearDown();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Set the current user context to be an admin.
     * @return $this
     */
    public function asAdmin()
    {
        return $this->actingAs($this->getAdmin());
    }

    /**
     * Get the current admin user.
     * @return mixed
     */
    public function getAdmin() {
        if($this->admin === null) {
            $adminRole = Role::getSystemRole('admin');
            $this->admin = $adminRole->users->first();
        }
        return $this->admin;
    }

    /**
     * Set the current editor context to be an editor.
     * @return $this
     */
    public function asEditor()
    {
        if ($this->editor === null) {
            $this->editor = $this->getEditor();
        }
        return $this->actingAs($this->editor);
    }

    /**
     * Get a user that's not a system user such as the guest user.
     */
    public function getNormalUser()
    {
        return \BookStack\User::where('system_name', '=', null)->get()->last();
    }

    /**
     * Quickly sets an array of settings.
     * @param $settingsArray
     */
    protected function setSettings($settingsArray)
    {
        $settings = app('BookStack\Services\SettingService');
        foreach ($settingsArray as $key => $value) {
            $settings->put($key, $value);
        }
    }

    /**
     * Create a group of entities that belong to a specific user.
     * @param $creatorUser
     * @param $updaterUser
     * @return array
     */
    protected function createEntityChainBelongingToUser($creatorUser, $updaterUser = false)
    {
        if ($updaterUser === false) $updaterUser = $creatorUser;
        $book = factory(\BookStack\Book::class)->create(['created_by' => $creatorUser->id, 'updated_by' => $updaterUser->id]);
        $chapter = factory(\BookStack\Chapter::class)->create(['created_by' => $creatorUser->id, 'updated_by' => $updaterUser->id, 'book_id' => $book->id]);
        $page = factory(\BookStack\Page::class)->create(['created_by' => $creatorUser->id, 'updated_by' => $updaterUser->id, 'book_id' => $book->id, 'chapter_id' => $chapter->id]);
        $restrictionService = $this->app[PermissionService::class];
        $restrictionService->buildJointPermissionsForEntity($book);
        return [
            'book' => $book,
            'chapter' => $chapter,
            'page' => $page
        ];
    }

    /**
     * Quick way to create a new user
     * @param array $attributes
     * @return mixed
     */
    protected function getEditor($attributes = [])
    {
        $user = factory(\BookStack\User::class)->create($attributes);
        $role = Role::getRole('editor');
        $user->attachRole($role);;
        return $user;
    }

    /**
     * Quick way to create a new user without any permissions
     * @param array $attributes
     * @return mixed
     */
    protected function getNewBlankUser($attributes = [])
    {
        $user = factory(\BookStack\User::class)->create($attributes);
        return $user;
    }

    /**
     * Assert that a given string is seen inside an element.
     *
     * @param  bool|string|null $element
     * @param  integer          $position
     * @param  string           $text
     * @param  bool             $negate
     * @return $this
     */
    protected function seeInNthElement($element, $position, $text, $negate = false)
    {
        $method = $negate ? 'assertNotRegExp' : 'assertRegExp';

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
     * @param  string  $uri
     * @return $this
     */
    protected function seePageUrlIs($uri)
    {
        $this->assertEquals(
            $uri, $this->currentUri, "Did not land on expected page [{$uri}].\n"
        );

        return $this;
    }

    /**
     * Do a forced visit that does not error out on exception.
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
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
     * @param $parentElement
     * @param $linkText
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
     * @param  string  $selector
     */
    protected function pageHasElement($selector)
    {
        $elements = $this->crawler->filter($selector);
        $this->assertTrue(count($elements) > 0, "The page does not contain an element matching " . $selector);
        return $this;
    }

    /**
     * Check if the page contains the given element.
     * @param  string  $selector
     */
    protected function pageNotHasElement($selector)
    {
        $elements = $this->crawler->filter($selector);
        $this->assertFalse(count($elements) > 0, "The page contains " . count($elements) . " elements matching " . $selector);
        return $this;
    }
}
