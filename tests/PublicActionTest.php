<?php

class PublicActionTest extends TestCase
{

    public function test_app_not_public()
    {
        $this->setSettings(['app-public' => 'false']);
        $book = \BookStack\Book::orderBy('name', 'asc')->first();
        $this->visit('/books')->seePageIs('/login');
        $this->visit($book->getUrl())->seePageIs('/login');

        $page = \BookStack\Page::first();
        $this->visit($page->getUrl())->seePageIs('/login');
    }

    public function test_books_viewable()
    {
        $this->setSettings(['app-public' => 'true']);
        $books = \BookStack\Book::orderBy('name', 'asc')->take(10)->get();
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
        $chapterToVisit = \BookStack\Chapter::first();
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
        $publicRole = \BookStack\Role::getSystemRole('public');
        // Grant all permissions to public
        $publicRole->permissions()->detach();
        foreach (\BookStack\RolePermission::all() as $perm) {
            $publicRole->attachPermission($perm);
        }
        $this->app[\BookStack\Services\PermissionService::class]->buildJointPermissionForRole($publicRole);

        $chapter = \BookStack\Chapter::first();
        $this->visit($chapter->book->getUrl());
        $this->visit($chapter->getUrl())
            ->click('New Page')
            ->see('New Page')
            ->seePageIs($chapter->getUrl('/create-page'));

        $this->submitForm('Continue', [
            'name' => 'My guest page'
        ])->seePageIs($chapter->book->getUrl('/page/my-guest-page/edit'));

        $user = \BookStack\User::getDefault();
        $this->seeInDatabase('pages', [
            'name' => 'My guest page',
            'chapter_id' => $chapter->id,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);
    }

    public function test_content_not_listed_on_404_for_public_users()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin()->visit($page->getUrl());
        Auth::logout();
        view()->share('pageTitle', '');
        $this->forceVisit('/cats/dogs/hippos');
        $this->dontSee($page->name);
    }

}