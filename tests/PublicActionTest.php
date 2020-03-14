<?php namespace Tests;

use Auth;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\Permissions\RolePermission;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Page;

class PublicActionTest extends BrowserKitTest
{

    public function test_app_not_public()
    {
        $this->setSettings(['app-public' => 'false']);
        $book = Book::orderBy('name', 'asc')->first();
        $this->visit('/books')->seePageIs('/login');
        $this->visit($book->getUrl())->seePageIs('/login');

        $page = Page::first();
        $this->visit($page->getUrl())->seePageIs('/login');
    }

    public function test_login_link_visible()
    {
        $this->setSettings(['app-public' => 'true']);
        $this->visit('/')->see(url('/login'));
    }

    public function test_register_link_visible_when_enabled()
    {
        $this->setSettings(['app-public' => 'true']);

        $this->visit('/')->see(url('/login'));
        $this->visit('/')->dontSee(url('/register'));

        $this->setSettings(['app-public' => 'true', 'registration-enabled' => 'true']);
        $this->visit('/')->see(url('/login'));
        $this->visit('/')->see(url('/register'));
    }

    public function test_books_viewable()
    {
        $this->setSettings(['app-public' => 'true']);
        $books = Book::orderBy('name', 'asc')->take(10)->get();
        $bookToVisit = $books[1];

        // Check books index page is showing
        $this->visit('/books')
            ->seeStatusCode(200)
            ->see($books[0]->name)
            // Check individual book page is showing and it's child contents are visible.
            ->click($bookToVisit->name)
            ->seePageIs($bookToVisit->getUrl())
            ->see($bookToVisit->name)
            ->see($bookToVisit->chapters()->first()->name);
    }

    public function test_chapters_viewable()
    {
        $this->setSettings(['app-public' => 'true']);
        $chapterToVisit = Chapter::first();
        $pageToVisit = $chapterToVisit->pages()->first();

        // Check chapters index page is showing
        $this->visit($chapterToVisit->getUrl())
            ->seeStatusCode(200)
            ->see($chapterToVisit->name)
            // Check individual chapter page is showing and it's child contents are visible.
            ->see($pageToVisit->name)
            ->click($pageToVisit->name)
            ->see($chapterToVisit->book->name)
            ->see($chapterToVisit->name)
            ->seePageIs($pageToVisit->getUrl());
    }

    public function test_public_page_creation()
    {
        $this->setSettings(['app-public' => 'true']);
        $publicRole = Role::getSystemRole('public');
        // Grant all permissions to public
        $publicRole->permissions()->detach();
        foreach (RolePermission::all() as $perm) {
            $publicRole->attachPermission($perm);
        }
        $this->app[PermissionService::class]->buildJointPermissionForRole($publicRole);

        $chapter = Chapter::first();
        $this->visit($chapter->book->getUrl());
        $this->visit($chapter->getUrl())
            ->click('New Page')
            ->see('New Page')
            ->seePageIs($chapter->getUrl('/create-page'));

        $this->submitForm('Continue', [
            'name' => 'My guest page'
        ])->seePageIs($chapter->book->getUrl('/page/my-guest-page/edit'));

        $user = User::getDefault();
        $this->seeInDatabase('pages', [
            'name' => 'My guest page',
            'chapter_id' => $chapter->id,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);
    }

    public function test_content_not_listed_on_404_for_public_users()
    {
        $page = Page::first();
        $this->asAdmin()->visit($page->getUrl());
        Auth::logout();
        view()->share('pageTitle', '');
        $this->forceVisit('/cats/dogs/hippos');
        $this->dontSee($page->name);
    }

    public function test_robots_effected_by_public_status()
    {
        $this->visit('/robots.txt');
        $this->seeText("User-agent: *\nDisallow: /");

        $this->setSettings(['app-public' => 'true']);
        $this->visit('/robots.txt');

        $this->seeText("User-agent: *\nDisallow:");
        $this->dontSeeText("Disallow: /");
    }

    public function test_robots_effected_by_setting()
    {
        $this->visit('/robots.txt');
        $this->seeText("User-agent: *\nDisallow: /");

        config()->set('app.allow_robots', true);
        $this->visit('/robots.txt');

        $this->seeText("User-agent: *\nDisallow:");
        $this->dontSeeText("Disallow: /");

        // Check config overrides app-public setting
        config()->set('app.allow_robots', false);
        $this->setSettings(['app-public' => 'true']);
        $this->visit('/robots.txt');

        $this->seeText("User-agent: *\nDisallow: /");
    }

    public function test_public_view_then_login_redirects_to_previous_content()
    {
        $this->setSettings(['app-public' => 'true']);
        $book = Book::query()->first();
        $this->visit($book->getUrl())
            ->see($book->name)
            ->visit('/login')
            ->type('admin@admin.com', '#email')
            ->type('password', '#password')
            ->press('Log In')
            ->seePageUrlIs($book->getUrl());
    }

    public function test_access_hidden_content_then_login_redirects_to_intended_content()
    {
        $this->setSettings(['app-public' => 'true']);
        $book = Book::query()->first();
        $this->setEntityRestrictions($book);

        try {
            $this->visit($book->getUrl());
        } catch (\Exception $exception) {}

        $this->see('Book not found')
            ->dontSee($book->name)
            ->visit('/login')
            ->type('admin@admin.com', '#email')
            ->type('password', '#password')
            ->press('Log In')
            ->seePageUrlIs($book->getUrl())
            ->see($book->name);
    }
}