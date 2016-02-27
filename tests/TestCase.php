<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    use DatabaseTransactions;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';
    private $admin;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function asAdmin()
    {
        if($this->admin === null) {
            $adminRole = \BookStack\Role::getRole('admin');
            $this->admin = $adminRole->users->first();
        }
        return $this->actingAs($this->admin);
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
        $book = factory(BookStack\Book::class)->create(['created_by' => $creatorUser->id, 'updated_by' => $updaterUser->id]);
        $chapter = factory(BookStack\Chapter::class)->create(['created_by' => $creatorUser->id, 'updated_by' => $updaterUser->id]);
        $page = factory(BookStack\Page::class)->create(['created_by' => $creatorUser->id, 'updated_by' => $updaterUser->id, 'book_id' => $book->id]);
        $book->chapters()->saveMany([$chapter]);
        $chapter->pages()->saveMany([$page]);
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
    protected function getNewUser($attributes = [])
    {
        $user = factory(\BookStack\User::class)->create($attributes);
        $userRepo = app('BookStack\Repos\UserRepo');
        $userRepo->attachDefaultRole($user);
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
}
