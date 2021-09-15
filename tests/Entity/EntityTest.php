<?php

namespace Tests\Entity;

use BookStack\Auth\UserRepo;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use Tests\BrowserKitTest;

class EntityTest extends BrowserKitTest
{
    public function test_entity_creation()
    {
        // Test Creation
        $book = $this->bookCreation();
        $chapter = $this->chapterCreation($book);
        $this->pageCreation($chapter);

        // Test Updating
        $this->bookUpdate($book);
    }

    public function bookUpdate(Book $book)
    {
        $newName = $book->name . ' Updated';
        $this->asAdmin()
            // Go to edit screen
            ->visit($book->getUrl() . '/edit')
            ->see($book->name)
            // Submit new name
            ->type($newName, '#name')
            ->press('Save Book')
            // Check page url and text
            ->seePageIs($book->getUrl() . '-updated')
            ->see($newName);

        return Book::find($book->id);
    }

    public function pageCreation($chapter)
    {
        $page = factory(Page::class)->make([
            'name' => 'My First Page',
        ]);

        $this->asAdmin()
            // Navigate to page create form
            ->visit($chapter->getUrl())
            ->click('New Page');

        $draftPage = Page::where('draft', '=', true)->orderBy('created_at', 'desc')->first();

        $this->seePageIs($draftPage->getUrl())
            // Fill out form
            ->type($page->name, '#name')
            ->type($page->html, '#html')
            ->press('Save Page')
            // Check redirect and page
            ->seePageIs($chapter->book->getUrl() . '/page/my-first-page')
            ->see($page->name);

        $page = Page::where('slug', '=', 'my-first-page')->where('chapter_id', '=', $chapter->id)->first();

        return $page;
    }

    public function chapterCreation(Book $book)
    {
        $chapter = factory(Chapter::class)->make([
            'name' => 'My First Chapter',
        ]);

        $this->asAdmin()
            // Navigate to chapter create page
            ->visit($book->getUrl())
            ->click('New Chapter')
            ->seePageIs($book->getUrl() . '/create-chapter')
            // Fill out form
            ->type($chapter->name, '#name')
            ->type($chapter->description, '#description')
            ->press('Save Chapter')
            // Check redirect and landing page
            ->seePageIs($book->getUrl() . '/chapter/my-first-chapter')
            ->see($chapter->name)->see($chapter->description);

        $chapter = Chapter::where('slug', '=', 'my-first-chapter')->where('book_id', '=', $book->id)->first();

        return $chapter;
    }

    public function bookCreation()
    {
        $book = factory(Book::class)->make([
            'name' => 'My First Book',
        ]);
        $this->asAdmin()
            ->visit('/books')
            // Choose to create a book
            ->click('Create New Book')
            ->seePageIs('/create-book')
            // Fill out form & save
            ->type($book->name, '#name')
            ->type($book->description, '#description')
            ->press('Save Book')
            // Check it redirects correctly
            ->seePageIs('/books/my-first-book')
            ->see($book->name)->see($book->description);

        // Ensure duplicate names are given different slugs
        $this->asAdmin()
            ->visit('/create-book')
            ->type($book->name, '#name')
            ->type($book->description, '#description')
            ->press('Save Book');

        $expectedPattern = '/\/books\/my-first-book-[0-9a-zA-Z]{3}/';
        $this->assertMatchesRegularExpression($expectedPattern, $this->currentUri, "Did not land on expected page [$expectedPattern].\n");

        $book = Book::where('slug', '=', 'my-first-book')->first();

        return $book;
    }

    public function test_entities_viewable_after_creator_deletion()
    {
        // Create required assets and revisions
        $creator = $this->getEditor();
        $updater = $this->getEditor();
        $entities = $this->createEntityChainBelongingToUser($creator, $updater);
        $this->actingAs($creator);
        app(UserRepo::class)->destroy($creator);
        app(PageRepo::class)->update($entities['page'], ['html' => '<p>hello!</p>>']);

        $this->checkEntitiesViewable($entities);
    }

    public function test_entities_viewable_after_updater_deletion()
    {
        // Create required assets and revisions
        $creator = $this->getEditor();
        $updater = $this->getEditor();
        $entities = $this->createEntityChainBelongingToUser($creator, $updater);
        $this->actingAs($updater);
        app(UserRepo::class)->destroy($updater);
        app(PageRepo::class)->update($entities['page'], ['html' => '<p>Hello there!</p>']);

        $this->checkEntitiesViewable($entities);
    }

    private function checkEntitiesViewable($entities)
    {
        // Check pages and books are visible.
        $this->asAdmin();
        $this->visit($entities['book']->getUrl())->seeStatusCode(200)
            ->visit($entities['chapter']->getUrl())->seeStatusCode(200)
            ->visit($entities['page']->getUrl())->seeStatusCode(200);
        // Check revision listing shows no errors.
        $this->visit($entities['page']->getUrl())
            ->click('Revisions')->seeStatusCode(200);
    }

}
