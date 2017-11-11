<?php namespace Tests;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Page;
use BookStack\Repos\EntityRepo;
use BookStack\Repos\UserRepo;

class EntityTest extends BrowserKitTest
{

    public function test_entity_creation()
    {

        // Test Creation
        $book = $this->bookCreation();
        $chapter = $this->chapterCreation($book);
        $page = $this->pageCreation($chapter);

        // Test Updating
        $book = $this->bookUpdate($book);

        // Test Deletion
        $this->bookDelete($book);
    }

    public function bookDelete(Book $book)
    {
        $this->asAdmin()
            ->visit($book->getUrl())
            // Check link works correctly
            ->click('Delete')
            ->seePageIs($book->getUrl() . '/delete')
            // Ensure the book name is show to user
            ->see($book->name)
            ->press('Confirm')
            ->seePageIs('/books')
            ->notSeeInDatabase('books', ['id' => $book->id]);
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

    public function test_book_sort_page_shows()
    {
        $books =  Book::all();
        $bookToSort = $books[0];
        $this->asAdmin()
            ->visit($bookToSort->getUrl())
            ->click('Sort')
            ->seePageIs($bookToSort->getUrl() . '/sort')
            ->seeStatusCode(200)
            ->see($bookToSort->name)
            // Ensure page shows other books
            ->see($books[1]->name);
    }

    public function test_book_sort_item_returns_book_content()
    {
        $books =  Book::all();
        $bookToSort = $books[0];
        $firstPage = $bookToSort->pages[0];
        $firstChapter = $bookToSort->chapters[0];
        $this->asAdmin()
            ->visit($bookToSort->getUrl() . '/sort-item')
            // Ensure book details are returned
            ->see($bookToSort->name)
            ->see($firstPage->name)
            ->see($firstChapter->name);
    }

    public function pageCreation($chapter)
    {
        $page = factory(Page::class)->make([
            'name' => 'My First Page'
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
            'name' => 'My First Chapter'
        ]);

        $this->asAdmin()
            // Navigate to chapter create page
            ->visit($book->getUrl())
            ->click('New Chapter')
            ->seePageIs($book->getUrl() . '/chapter/create')
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
            'name' => 'My First Book'
        ]);
        $this->asAdmin()
            ->visit('/books')
            // Choose to create a book
            ->click('Create New Book')
            ->seePageIs('/books/create')
            // Fill out form & save
            ->type($book->name, '#name')
            ->type($book->description, '#description')
            ->press('Save Book')
            // Check it redirects correctly
            ->seePageIs('/books/my-first-book')
            ->see($book->name)->see($book->description);

        // Ensure duplicate names are given different slugs
        $this->asAdmin()
            ->visit('/books/create')
            ->type($book->name, '#name')
            ->type($book->description, '#description')
            ->press('Save Book');
        
        $expectedPattern = '/\/books\/my-first-book-[0-9a-zA-Z]{3}/';
        $this->assertRegExp($expectedPattern, $this->currentUri, "Did not land on expected page [$expectedPattern].\n");

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
        app(EntityRepo::class)->savePageRevision($entities['page']);

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
        app(EntityRepo::class)->savePageRevision($entities['page']);

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

    public function test_recently_created_pages_view()
    {
        $user = $this->getEditor();
        $content = $this->createEntityChainBelongingToUser($user);

        $this->asAdmin()->visit('/pages/recently-created')
            ->seeInNthElement('.entity-list .page', 0, $content['page']->name);
    }

    public function test_recently_updated_pages_view()
    {
        $user = $this->getEditor();
        $content = $this->createEntityChainBelongingToUser($user);

        $this->asAdmin()->visit('/pages/recently-updated')
            ->seeInNthElement('.entity-list .page', 0, $content['page']->name);
    }

    public function test_old_page_slugs_redirect_to_new_pages()
    {
        $page = Page::first();
        $pageUrl = $page->getUrl();
        $newPageUrl = '/books/' . $page->book->slug . '/page/super-test-page';
        // Need to save twice since revisions are not generated in seeder.
        $this->asAdmin()->visit($pageUrl)
            ->clickInElement('#content', 'Edit')
            ->type('super test', '#name')
            ->press('Save Page');

        $page = Page::first();
        $pageUrl = $page->getUrl();

        // Second Save
        $this->visit($pageUrl)
            ->clickInElement('#content', 'Edit')
            ->type('super test page', '#name')
            ->press('Save Page')
            // Check redirect
            ->seePageIs($newPageUrl);

        $this->visit($pageUrl)
            ->seePageIs($newPageUrl);
    }

    public function test_recently_updated_pages_on_home()
    {
        $page = Page::orderBy('updated_at', 'asc')->first();
        $this->asAdmin()->visit('/')
            ->dontSeeInElement('#recently-updated-pages', $page->name);
        $this->visit($page->getUrl() . '/edit')
            ->press('Save Page')
            ->visit('/')
            ->seeInElement('#recently-updated-pages', $page->name);
    }

    public function test_slug_multi_byte_lower_casing()
    {
        $entityRepo = app(EntityRepo::class);
        $book = $entityRepo->createFromInput('book', [
            'name' => 'КНИГА'
        ]);

        $this->assertEquals('книга', $book->slug);
    }


}
