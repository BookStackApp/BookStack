<?php

namespace Tests\Entity;

use BookStack\Actions\ActivityType;
use BookStack\Actions\Tag;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class ConvertTest extends TestCase
{
    public function test_chapter_edit_view_shows_convert_option()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();

        $resp = $this->asEditor()->get($chapter->getUrl('/edit'));
        $resp->assertSee('Convert to Book');
        $resp->assertSee('Convert Chapter');
        $resp->assertElementExists('form[action$="/convert-to-book"] button');
    }

    public function test_convert_chapter_to_book()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        $chapter->tags()->save(new Tag(['name' => 'Category', 'value' => 'Penguins']));
        /** @var Page $childPage */
        $childPage = $chapter->pages()->first();

        $resp = $this->asEditor()->post($chapter->getUrl('/convert-to-book'));
        $resp->assertRedirectContains('/books/');

        /** @var Book $newBook */
        $newBook = Book::query()->orderBy('id', 'desc')->first();

        $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
        $this->assertDatabaseHas('pages', ['id' => $childPage->id, 'book_id' => $newBook->id, 'chapter_id' => 0]);
        $this->assertCount(1, $newBook->tags);
        $this->assertEquals('Category', $newBook->tags->first()->name);
        $this->assertEquals('Penguins', $newBook->tags->first()->value);
        $this->assertEquals($chapter->name, $newBook->name);
        $this->assertEquals($chapter->description, $newBook->description);

        $this->assertActivityExists(ActivityType::BOOK_CREATE_FROM_CHAPTER, $newBook);
    }

    public function test_convert_chapter_to_book_requires_permissions()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $user = $this->getViewer();

        $permissions = ['chapter-delete-all', 'book-create-all', 'chapter-update-all'];
        $this->giveUserPermissions($user, $permissions);

        foreach ($permissions as $permission) {
            $this->removePermissionFromUser($user, $permission);
            $resp = $this->actingAs($user)->post($chapter->getUrl('/convert-to-book'));
            $this->assertPermissionError($resp);
            $this->giveUserPermissions($user, [$permission]);
        }

        $resp = $this->actingAs($user)->post($chapter->getUrl('/convert-to-book'));
        $this->assertNotPermissionError($resp);
        $resp->assertRedirect();
    }

    public function test_book_edit_view_shows_convert_option()
    {
        $book = Book::query()->first();

        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $resp->assertSee('Convert to Shelf');
        $resp->assertSee('Convert Book');
        $resp->assertSee('Note that permissions on shelves do not auto-cascade to content');
        $resp->assertElementExists('form[action$="/convert-to-shelf"] button');
    }

    public function test_book_convert_to_shelf()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('directPages')->whereHas('chapters')->firstOrFail();
        $book->tags()->save(new Tag(['name' => 'Category', 'value' => 'Ducks']));
        /** @var Page $childPage */
        $childPage = $book->directPages()->first();
        /** @var Chapter $childChapter */
        $childChapter = $book->chapters()->whereHas('pages')->firstOrFail();
        /** @var Page $chapterChildPage */
        $chapterChildPage = $childChapter->pages()->firstOrFail();
        $bookChapterCount = $book->chapters()->count();
        $systemBookCount = Book::query()->count();

        // Run conversion
        $resp = $this->asEditor()->post($book->getUrl('/convert-to-shelf'));

        /** @var Bookshelf $newShelf */
        $newShelf = Bookshelf::query()->orderBy('id', 'desc')->first();

        // Checks for new shelf
        $resp->assertRedirectContains('/shelves/');
        $this->assertDatabaseMissing('chapters', ['id' => $childChapter->id]);
        $this->assertCount(1, $newShelf->tags);
        $this->assertEquals('Category', $newShelf->tags->first()->name);
        $this->assertEquals('Ducks', $newShelf->tags->first()->value);
        $this->assertEquals($book->name, $newShelf->name);
        $this->assertEquals($book->description, $newShelf->description);
        $this->assertEquals($newShelf->books()->count(), $bookChapterCount + 1);
        $this->assertEquals($systemBookCount + $bookChapterCount, Book::query()->count());
        $this->assertActivityExists(ActivityType::BOOKSHELF_CREATE_FROM_BOOK, $newShelf);

        // Checks for old book to contain child pages
        $this->assertDatabaseHas('books', ['id' => $book->id, 'name' => $book->name . ' Pages']);
        $this->assertDatabaseHas('pages', ['id' => $childPage->id, 'book_id' => $book->id, 'chapter_id' => 0]);

        // Checks for nested page
        $chapterChildPage->refresh();
        $this->assertEquals(0, $chapterChildPage->chapter_id);
        $this->assertEquals($childChapter->name, $chapterChildPage->book->name);
    }

    public function test_book_convert_to_shelf_requires_permissions()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $user = $this->getViewer();

        $permissions = ['book-delete-all', 'bookshelf-create-all', 'book-update-all', 'book-create-all'];
        $this->giveUserPermissions($user, $permissions);

        foreach ($permissions as $permission) {
            $this->removePermissionFromUser($user, $permission);
            $resp = $this->actingAs($user)->post($book->getUrl('/convert-to-shelf'));
            $this->assertPermissionError($resp);
            $this->giveUserPermissions($user, [$permission]);
        }

        $resp = $this->actingAs($user)->post($book->getUrl('/convert-to-shelf'));
        $this->assertNotPermissionError($resp);
        $resp->assertRedirect();
    }
}
