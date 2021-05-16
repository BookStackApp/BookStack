<?php

use BookStack\Actions\Favourite;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class FavouriteTest extends TestCase
{

    public function test_page_add_favourite_flow()
    {
        $page = Page::query()->first();
        $editor = $this->getEditor();

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertElementContains('button', 'Favourite');
        $resp->assertElementExists('form[method="POST"][action$="/favourites/add"]');

        $resp = $this->post('/favourites/add', [
            'type' => get_class($page),
            'id' => $page->id,
        ]);
        $resp->assertRedirect($page->getUrl());
        $resp->assertSessionHas('success', "\"{$page->name}\" has been added to your favourites");

        $this->assertDatabaseHas('favourites', [
            'user_id' => $editor->id,
            'favouritable_type' => $page->getMorphClass(),
            'favouritable_id' => $page->id,
        ]);
    }

    public function test_page_remove_favourite_flow()
    {
        $page = Page::query()->first();
        $editor = $this->getEditor();
        Favourite::query()->forceCreate([
            'user_id' => $editor->id,
            'favouritable_id' => $page->id,
            'favouritable_type' => $page->getMorphClass(),
        ]);

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertElementContains('button', 'Unfavourite');
        $resp->assertElementExists('form[method="POST"][action$="/favourites/remove"]');

        $resp = $this->post('/favourites/remove', [
            'type' => get_class($page),
            'id' => $page->id,
        ]);
        $resp->assertRedirect($page->getUrl());
        $resp->assertSessionHas('success', "\"{$page->name}\" has been removed from your favourites");

        $this->assertDatabaseMissing('favourites', [
            'user_id' => $editor->id,
        ]);
    }

}