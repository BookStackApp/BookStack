<?php namespace Tests\User;

use Activity;
use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
use BookStack\Entities\Models\Bookshelf;
use Tests\BrowserKitTest;

class UserProfileTest extends BrowserKitTest
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::all()->last();
    }

    public function test_profile_page_shows_name()
    {
        $this->asAdmin()
            ->visit('/user/' . $this->user->slug)
            ->see($this->user->name);
    }

    public function test_profile_page_shows_recent_entities()
    {
        $content = $this->createEntityChainBelongingToUser($this->user, $this->user);

        $this->asAdmin()
            ->visit('/user/' . $this->user->slug)
            // Check the recently created page is shown
            ->see($content['page']->name)
            // Check the recently created chapter is shown
            ->see($content['chapter']->name)
            // Check the recently created book is shown
            ->see($content['book']->name);
    }

    public function test_profile_page_shows_created_content_counts()
    {
        $newUser = $this->getNewBlankUser();

        $this->asAdmin()->visit('/user/' . $newUser->slug)
            ->see($newUser->name)
            ->seeInElement('#content-counts', '0 Books')
            ->seeInElement('#content-counts', '0 Chapters')
            ->seeInElement('#content-counts', '0 Pages');

        $this->createEntityChainBelongingToUser($newUser, $newUser);

        $this->asAdmin()->visit('/user/' . $newUser->slug)
            ->see($newUser->name)
            ->seeInElement('#content-counts', '1 Book')
            ->seeInElement('#content-counts', '1 Chapter')
            ->seeInElement('#content-counts', '1 Page');
    }

    public function test_profile_page_shows_recent_activity()
    {
        $newUser = $this->getNewBlankUser();
        $this->actingAs($newUser);
        $entities = $this->createEntityChainBelongingToUser($newUser, $newUser);
        Activity::addForEntity($entities['book'], ActivityType::BOOK_UPDATE);
        Activity::addForEntity($entities['page'], ActivityType::PAGE_CREATE);

        $this->asAdmin()->visit('/user/' . $newUser->slug)
            ->seeInElement('#recent-user-activity', 'updated book')
            ->seeInElement('#recent-user-activity', 'created page')
            ->seeInElement('#recent-user-activity', $entities['page']->name);
    }

    public function test_clicking_user_name_in_activity_leads_to_profile_page()
    {
        $newUser = $this->getNewBlankUser();
        $this->actingAs($newUser);
        $entities = $this->createEntityChainBelongingToUser($newUser, $newUser);
        Activity::addForEntity($entities['book'], ActivityType::BOOK_UPDATE);
        Activity::addForEntity($entities['page'], ActivityType::PAGE_CREATE);

        $this->asAdmin()->visit('/')->clickInElement('#recent-activity', $newUser->name)
            ->seePageIs('/user/' . $newUser->slug)
            ->see($newUser->name);
    }

    public function test_profile_has_search_links_in_created_entity_lists()
    {
        $user = $this->getEditor();
        $resp = $this->actingAs($this->getAdmin())->visit('/user/' . $user->slug);

        $expectedLinks = [
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Apage%7D',
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Achapter%7D',
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Abook%7D',
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Abookshelf%7D',
        ];

        foreach ($expectedLinks as $link) {
            $resp->seeInElement('[href$="' . $link . '"]', 'View All');
        }
    }

    public function test_guest_profile_shows_limited_form()
    {
        $this->asAdmin()
            ->visit('/settings/users')
            ->click('Guest')
            ->dontSeeElement('#password');
    }

    public function test_guest_profile_cannot_be_deleted()
    {
        $guestUser = User::getDefault();
        $this->asAdmin()->visit('/settings/users/' . $guestUser->id . '/delete')
            ->see('Delete User')->see('Guest')
            ->press('Confirm')
            ->seePageIs('/settings/users/' . $guestUser->id)
            ->see('cannot delete the guest user');
    }

    public function test_books_view_is_list()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'books_view_type', 'list');

        $this->actingAs($editor)
            ->visit('/books')
            ->pageNotHasElement('.featured-image-container')
            ->pageHasElement('.content-wrap .entity-list-item');
    }

    public function test_books_view_is_grid()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'books_view_type', 'grid');

        $this->actingAs($editor)
            ->visit('/books')
            ->pageHasElement('.featured-image-container');
    }

    public function test_shelf_view_type_change()
    {
        $editor = $this->getEditor();
        $shelf = Bookshelf::query()->first();
        setting()->putUser($editor, 'bookshelf_view_type', 'list');

        $this->actingAs($editor)->visit($shelf->getUrl())
            ->pageNotHasElement('.featured-image-container')
            ->pageHasElement('.content-wrap .entity-list-item')
            ->see('Grid View');

        $req = $this->patch("/settings/users/{$editor->id}/switch-shelf-view", ['view_type' => 'grid']);
        $req->assertRedirectedTo($shelf->getUrl());

        $this->actingAs($editor)->visit($shelf->getUrl())
            ->pageHasElement('.featured-image-container')
            ->pageNotHasElement('.content-wrap .entity-list-item')
            ->see('List View');
    }

}
