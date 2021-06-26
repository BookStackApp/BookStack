<?php

namespace Tests;

use Auth;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\Permissions\RolePermission;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\View;

class PublicActionTest extends TestCase
{
    public function test_app_not_public()
    {
        $this->setSettings(['app-public' => 'false']);
        $book = Book::query()->first();
        $this->get('/books')->assertRedirect('/login');
        $this->get($book->getUrl())->assertRedirect('/login');

        $page = Page::query()->first();
        $this->get($page->getUrl())->assertRedirect('/login');
    }

    public function test_login_link_visible()
    {
        $this->setSettings(['app-public' => 'true']);
        $this->get('/')->assertElementExists('a[href="' . url('/login') . '"]');
    }

    public function test_register_link_visible_when_enabled()
    {
        $this->setSettings(['app-public' => 'true']);
        $home = $this->get('/');
        $home->assertSee(url('/login'));
        $home->assertDontSee(url('/register'));

        $this->setSettings(['app-public' => 'true', 'registration-enabled' => 'true']);
        $home = $this->get('/');
        $home->assertSee(url('/login'));
        $home->assertSee(url('/register'));
    }

    public function test_books_viewable()
    {
        $this->setSettings(['app-public' => 'true']);
        $books = Book::query()->orderBy('name', 'asc')->take(10)->get();
        $bookToVisit = $books[1];

        // Check books index page is showing
        $resp = $this->get('/books');
        $resp->assertStatus(200);
        $resp->assertSee($books[0]->name);

        // Check individual book page is showing and it's child contents are visible.
        $resp = $this->get($bookToVisit->getUrl());
        $resp->assertSee($bookToVisit->name);
        $resp->assertSee($bookToVisit->chapters()->first()->name);
    }

    public function test_chapters_viewable()
    {
        $this->setSettings(['app-public' => 'true']);
        /** @var Chapter $chapterToVisit */
        $chapterToVisit = Chapter::query()->first();
        $pageToVisit = $chapterToVisit->pages()->first();

        // Check chapters index page is showing
        $resp = $this->get($chapterToVisit->getUrl());
        $resp->assertStatus(200);
        $resp->assertSee($chapterToVisit->name);
        // Check individual chapter page is showing and it's child contents are visible.
        $resp->assertSee($pageToVisit->name);
        $resp = $this->get($pageToVisit->getUrl());
        $resp->assertStatus(200);
        $resp->assertSee($chapterToVisit->book->name);
        $resp->assertSee($chapterToVisit->name);
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

        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $resp = $this->get($chapter->getUrl());
        $resp->assertSee('New Page');
        $resp->assertElementExists('a[href="' . $chapter->getUrl('/create-page') . '"]');

        $resp = $this->get($chapter->getUrl('/create-page'));
        $resp->assertSee('Continue');
        $resp->assertSee('Page Name');
        $resp->assertElementExists('form[action="' . $chapter->getUrl('/create-guest-page') . '"]');

        $resp = $this->post($chapter->getUrl('/create-guest-page'), ['name' => 'My guest page']);
        $resp->assertRedirect($chapter->book->getUrl('/page/my-guest-page/edit'));

        $user = User::getDefault();
        $this->assertDatabaseHas('pages', [
            'name'       => 'My guest page',
            'chapter_id' => $chapter->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    public function test_content_not_listed_on_404_for_public_users()
    {
        $page = Page::query()->first();
        $page->fill(['name' => 'my testing random unique page name'])->save();
        $this->asAdmin()->get($page->getUrl()); // Fake visit to show on recents
        $resp = $this->get('/cats/dogs/hippos');
        $resp->assertStatus(404);
        $resp->assertSee($page->name);
        View::share('pageTitle', '');

        Auth::logout();
        $resp = $this->get('/cats/dogs/hippos');
        $resp->assertStatus(404);
        $resp->assertDontSee($page->name);
    }

    public function test_robots_effected_by_public_status()
    {
        $this->get('/robots.txt')->assertSee("User-agent: *\nDisallow: /");

        $this->setSettings(['app-public' => 'true']);

        $resp = $this->get('/robots.txt');
        $resp->assertSee("User-agent: *\nDisallow:");
        $resp->assertDontSee('Disallow: /');
    }

    public function test_robots_effected_by_setting()
    {
        $this->get('/robots.txt')->assertSee("User-agent: *\nDisallow: /");

        config()->set('app.allow_robots', true);

        $resp = $this->get('/robots.txt');
        $resp->assertSee("User-agent: *\nDisallow:");
        $resp->assertDontSee('Disallow: /');

        // Check config overrides app-public setting
        config()->set('app.allow_robots', false);
        $this->setSettings(['app-public' => 'true']);
        $this->get('/robots.txt')->assertSee("User-agent: *\nDisallow: /");
    }

    public function test_public_view_then_login_redirects_to_previous_content()
    {
        $this->setSettings(['app-public' => 'true']);
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->get($book->getUrl());
        $resp->assertSee($book->name);

        $this->get('/login');
        $resp = $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $resp->assertRedirect($book->getUrl());
    }

    public function test_access_hidden_content_then_login_redirects_to_intended_content()
    {
        $this->setSettings(['app-public' => 'true']);
        /** @var Book $book */
        $book = Book::query()->first();
        $this->setEntityRestrictions($book);

        $resp = $this->get($book->getUrl());
        $resp->assertSee('Book not found');

        $this->get('/login');
        $resp = $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $resp->assertRedirect($book->getUrl());
        $this->followRedirects($resp)->assertSee($book->name);
    }
}
