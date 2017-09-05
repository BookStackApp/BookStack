<?php namespace Tests;

class UserProfileTest extends BrowserKitTest
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = \BookStack\User::all()->last();
    }

    public function test_profile_page_shows_name()
    {
        $this->asAdmin()
            ->visit('/user/' . $this->user->id)
            ->see($this->user->name);
    }

    public function test_profile_page_shows_recent_entities()
    {
        $content = $this->createEntityChainBelongingToUser($this->user, $this->user);

        $this->asAdmin()
            ->visit('/user/' . $this->user->id)
            // Check the recently created page is shown
            ->see($content['page']->name)
            // Check the recently created chapter is shown
            ->see($content['chapter']->name)
            // Check the recently created book is shown
            ->see($content['book']->name);
    }

    public function test_profile_page_shows_created_content_counts()
    {
        $newUser = $this->getEditor();

        $this->asAdmin()->visit('/user/' . $newUser->id)
            ->see($newUser->name)
            ->seeInElement('#content-counts', '0 Books')
            ->seeInElement('#content-counts', '0 Chapters')
            ->seeInElement('#content-counts', '0 Pages');

        $this->createEntityChainBelongingToUser($newUser, $newUser);

        $this->asAdmin()->visit('/user/' . $newUser->id)
            ->see($newUser->name)
            ->seeInElement('#content-counts', '1 Book')
            ->seeInElement('#content-counts', '1 Chapter')
            ->seeInElement('#content-counts', '1 Page');
    }

    public function test_profile_page_shows_recent_activity()
    {
        $newUser = $this->getEditor();
        $this->actingAs($newUser);
        $entities = $this->createEntityChainBelongingToUser($newUser, $newUser);
        \Activity::add($entities['book'], 'book_update', $entities['book']->id);
        \Activity::add($entities['page'], 'page_create', $entities['book']->id);

        $this->asAdmin()->visit('/user/' . $newUser->id)
            ->seeInElement('#recent-activity', 'updated book')
            ->seeInElement('#recent-activity', 'created page')
            ->seeInElement('#recent-activity', $entities['page']->name);
    }

    public function test_clicking_user_name_in_activity_leads_to_profile_page()
    {
        $newUser = $this->getEditor();
        $this->actingAs($newUser);
        $entities = $this->createEntityChainBelongingToUser($newUser, $newUser);
        \Activity::add($entities['book'], 'book_update', $entities['book']->id);
        \Activity::add($entities['page'], 'page_create', $entities['book']->id);

        $this->asAdmin()->visit('/')->clickInElement('#recent-activity', $newUser->name)
            ->seePageIs('/user/' . $newUser->id)
            ->see($newUser->name);
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
        $guestUser = \BookStack\User::getDefault();
        $this->asAdmin()->visit('/settings/users/' . $guestUser->id . '/delete')
            ->see('Delete User')->see('Guest')
            ->press('Confirm')
            ->seePageIs('/settings/users/' . $guestUser->id)
            ->see('cannot delete the guest user');
    }

    public function test_books_view_is_list()
    {
        $editor = $this->getEditor([
          'books_view_type' => 'list'
        ]);

        $this->actingAs($editor)
            ->visit('/books')
            ->pageNotHasElement('.featured-image-container')
            ->pageHasElement('.entity-list-item');
    }

    public function test_books_view_is_grid()
    {
        $editor = $this->getEditor([
          'books_view_type' => 'grid'
        ]);

        $this->actingAs($editor)
            ->visit('/books')
            ->pageHasElement('.featured-image-container');
    }
}
