<?php

namespace Tests;

use BookStack\Activity\Models\Favourite;
use BookStack\Users\Models\User;

class FavouriteTest extends TestCase
{
    public function test_page_add_favourite_flow()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $this->withHtml($resp)->assertElementContains('button', 'Favourite');
        $this->withHtml($resp)->assertElementExists('form[method="POST"][action$="/favourites/add"]');

        $resp = $this->post('/favourites/add', [
            'type' => get_class($page),
            'id'   => $page->id,
        ]);
        $resp->assertRedirect($page->getUrl());
        $resp->assertSessionHas('success', "\"{$page->name}\" has been added to your favourites");

        $this->assertDatabaseHas('favourites', [
            'user_id'           => $editor->id,
            'favouritable_type' => $page->getMorphClass(),
            'favouritable_id'   => $page->id,
        ]);
    }

    public function test_page_remove_favourite_flow()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();
        Favourite::query()->forceCreate([
            'user_id'           => $editor->id,
            'favouritable_id'   => $page->id,
            'favouritable_type' => $page->getMorphClass(),
        ]);

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $this->withHtml($resp)->assertElementContains('button', 'Unfavourite');
        $this->withHtml($resp)->assertElementExists('form[method="POST"][action$="/favourites/remove"]');

        $resp = $this->post('/favourites/remove', [
            'type' => get_class($page),
            'id'   => $page->id,
        ]);
        $resp->assertRedirect($page->getUrl());
        $resp->assertSessionHas('success', "\"{$page->name}\" has been removed from your favourites");

        $this->assertDatabaseMissing('favourites', [
            'user_id' => $editor->id,
        ]);
    }

    public function test_favourite_flow_with_own_permissions()
    {
        $book = $this->entities->book();
        $user = User::factory()->create();
        $book->owned_by = $user->id;
        $book->save();

        $this->permissions->grantUserRolePermissions($user, ['book-view-own']);

        $this->actingAs($user)->get($book->getUrl());
        $resp = $this->post('/favourites/add', [
            'type' => get_class($book),
            'id'   => $book->id,
        ]);
        $resp->assertRedirect($book->getUrl());

        $this->assertDatabaseHas('favourites', [
            'user_id'           => $user->id,
            'favouritable_type' => $book->getMorphClass(),
            'favouritable_id'   => $book->id,
        ]);
    }

    public function test_each_entity_type_shows_favourite_button()
    {
        $this->actingAs($this->users->editor());

        foreach ($this->entities->all() as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementExists('form[method="POST"][action$="/favourites/add"]');
        }
    }

    public function test_header_contains_link_to_favourites_page_when_logged_in()
    {
        $this->setSettings(['app-public' => 'true']);
        $resp = $this->get('/');
        $this->withHtml($resp)->assertElementNotContains('header', 'My Favourites');
        $resp = $this->actingAs($this->users->viewer())->get('/');
        $this->withHtml($resp)->assertElementContains('header a', 'My Favourites');
    }

    public function test_favourites_shown_on_homepage()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/');
        $this->withHtml($resp)->assertElementNotExists('#top-favourites');

        $page = $this->entities->page();
        $page->favourites()->save((new Favourite())->forceFill(['user_id' => $editor->id]));

        $resp = $this->get('/');
        $this->withHtml($resp)->assertElementExists('#top-favourites');
        $this->withHtml($resp)->assertElementContains('#top-favourites', $page->name);
    }

    public function test_favourites_list_page_shows_favourites_and_has_working_pagination()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/favourites');
        $resp->assertDontSee($page->name);

        $page->favourites()->save((new Favourite())->forceFill(['user_id' => $editor->id]));

        $resp = $this->get('/favourites');
        $resp->assertSee($page->name);

        $resp = $this->get('/favourites?page=2');
        $resp->assertDontSee($page->name);
    }
}
