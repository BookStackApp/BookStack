<?php

namespace Tests\User;

use Activity;
use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::all()->last();
    }

    public function test_profile_page_shows_name()
    {
        $this->asAdmin()
            ->get('/user/' . $this->user->slug)
            ->assertSee($this->user->name);
    }

    public function test_profile_page_shows_recent_entities()
    {
        $content = $this->entities->createChainBelongingToUser($this->user, $this->user);

        $resp = $this->asAdmin()->get('/user/' . $this->user->slug);
        // Check the recently created page is shown
        $resp->assertSee($content['page']->name);
        // Check the recently created chapter is shown
        $resp->assertSee($content['chapter']->name);
        // Check the recently created book is shown
        $resp->assertSee($content['book']->name);
    }

    public function test_profile_page_shows_created_content_counts()
    {
        $newUser = User::factory()->create();

        $resp = $this->asAdmin()->get('/user/' . $newUser->slug)
            ->assertSee($newUser->name);
        $this->withHtml($resp)->assertElementContains('#content-counts', '0 Books')
            ->assertElementContains('#content-counts', '0 Chapters')
            ->assertElementContains('#content-counts', '0 Pages');

        $this->entities->createChainBelongingToUser($newUser, $newUser);

        $resp = $this->asAdmin()->get('/user/' . $newUser->slug)
            ->assertSee($newUser->name);
        $this->withHtml($resp)->assertElementContains('#content-counts', '1 Book')
            ->assertElementContains('#content-counts', '1 Chapter')
            ->assertElementContains('#content-counts', '1 Page');
    }

    public function test_profile_page_shows_recent_activity()
    {
        $newUser = User::factory()->create();
        $this->actingAs($newUser);
        $entities = $this->entities->createChainBelongingToUser($newUser, $newUser);
        Activity::add(ActivityType::BOOK_UPDATE, $entities['book']);
        Activity::add(ActivityType::PAGE_CREATE, $entities['page']);

        $resp = $this->asAdmin()->get('/user/' . $newUser->slug);
        $this->withHtml($resp)->assertElementContains('#recent-user-activity', 'updated book')
            ->assertElementContains('#recent-user-activity', 'created page')
            ->assertElementContains('#recent-user-activity', $entities['page']->name);
    }

    public function test_user_activity_has_link_leading_to_profile()
    {
        $newUser = User::factory()->create();
        $this->actingAs($newUser);
        $entities = $this->entities->createChainBelongingToUser($newUser, $newUser);
        Activity::add(ActivityType::BOOK_UPDATE, $entities['book']);
        Activity::add(ActivityType::PAGE_CREATE, $entities['page']);

        $linkSelector = '#recent-activity a[href$="/user/' . $newUser->slug . '"]';
        $resp = $this->asAdmin()->get('/');
        $this->withHtml($resp)->assertElementContains($linkSelector, $newUser->name);
    }

    public function test_profile_has_search_links_in_created_entity_lists()
    {
        $user = $this->getEditor();
        $resp = $this->actingAs($this->getAdmin())->get('/user/' . $user->slug);

        $expectedLinks = [
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Apage%7D',
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Achapter%7D',
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Abook%7D',
            '/search?term=%7Bcreated_by%3A' . $user->slug . '%7D+%7Btype%3Abookshelf%7D',
        ];

        foreach ($expectedLinks as $link) {
            $this->withHtml($resp)->assertElementContains('[href$="' . $link . '"]', 'View All');
        }
    }
}
