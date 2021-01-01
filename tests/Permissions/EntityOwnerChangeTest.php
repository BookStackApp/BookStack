<?php namespace Tests\Permissions;

use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Str;
use Tests\TestCase;

class EntityOwnerChangeTest extends TestCase
{

    public function test_changing_page_owner()
    {
        $page = Page::query()->first();
        $user = User::query()->where('id', '!=', $page->owned_by)->first();

        $this->asAdmin()->put($page->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('pages', ['owned_by' => $user->id, 'id' => $page->id]);
    }

    public function test_changing_chapter_owner()
    {
        $chapter = Chapter::query()->first();
        $user = User::query()->where('id', '!=', $chapter->owned_by)->first();

        $this->asAdmin()->put($chapter->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('chapters', ['owned_by' => $user->id, 'id' => $chapter->id]);
    }

    public function test_changing_book_owner()
    {
        $book = Book::query()->first();
        $user = User::query()->where('id', '!=', $book->owned_by)->first();

        $this->asAdmin()->put($book->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('books', ['owned_by' => $user->id, 'id' => $book->id]);
    }

    public function test_changing_shelf_owner()
    {
        $shelf = Bookshelf::query()->first();
        $user = User::query()->where('id', '!=', $shelf->owned_by)->first();

        $this->asAdmin()->put($shelf->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('bookshelves', ['owned_by' => $user->id, 'id' => $shelf->id]);
    }

}