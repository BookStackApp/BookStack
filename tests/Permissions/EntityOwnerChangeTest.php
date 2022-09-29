<?php

namespace Tests\Permissions;

use BookStack\Auth\User;
use Tests\TestCase;

class EntityOwnerChangeTest extends TestCase
{
    public function test_changing_page_owner()
    {
        $page = $this->entities->page();
        $user = User::query()->where('id', '!=', $page->owned_by)->first();

        $this->asAdmin()->put($page->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('pages', ['owned_by' => $user->id, 'id' => $page->id]);
    }

    public function test_changing_chapter_owner()
    {
        $chapter = $this->entities->chapter();
        $user = User::query()->where('id', '!=', $chapter->owned_by)->first();

        $this->asAdmin()->put($chapter->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('chapters', ['owned_by' => $user->id, 'id' => $chapter->id]);
    }

    public function test_changing_book_owner()
    {
        $book = $this->entities->book();
        $user = User::query()->where('id', '!=', $book->owned_by)->first();

        $this->asAdmin()->put($book->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('books', ['owned_by' => $user->id, 'id' => $book->id]);
    }

    public function test_changing_shelf_owner()
    {
        $shelf = $this->entities->shelf();
        $user = User::query()->where('id', '!=', $shelf->owned_by)->first();

        $this->asAdmin()->put($shelf->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('bookshelves', ['owned_by' => $user->id, 'id' => $shelf->id]);
    }
}
