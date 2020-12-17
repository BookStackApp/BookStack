<?php namespace Tests\Permissions;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
use Tests\BrowserKitTest;

class RestrictionsTest extends BrowserKitTest
{

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $viewer;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getEditor();
        $this->viewer = $this->getViewer();
    }

    protected function setEntityRestrictions(Entity $entity, $actions = [], $roles = [])
    {
        $roles = [
            $this->user->roles->first(),
            $this->viewer->roles->first(),
        ];
        parent::setEntityRestrictions($entity, $actions, $roles);
    }

    public function test_bookshelf_view_restriction()
    {
        $shelf = Bookshelf::first();

        $this->actingAs($this->user)
            ->visit($shelf->getUrl())
            ->seePageIs($shelf->getUrl());

        $this->setEntityRestrictions($shelf, []);

        $this->forceVisit($shelf->getUrl())
            ->see('Bookshelf not found');

        $this->setEntityRestrictions($shelf, ['view']);

        $this->visit($shelf->getUrl())
            ->see($shelf->name);
    }

    public function test_bookshelf_update_restriction()
    {
        $shelf = Bookshelf::first();

        $this->actingAs($this->user)
            ->visit($shelf->getUrl('/edit'))
            ->see('Edit Book');

        $this->setEntityRestrictions($shelf, ['view', 'delete']);

        $this->forceVisit($shelf->getUrl('/edit'))
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($shelf, ['view', 'update']);

        $this->visit($shelf->getUrl('/edit'))
            ->seePageIs($shelf->getUrl('/edit'));
    }

    public function test_bookshelf_delete_restriction()
    {
        $shelf = Book::first();

        $this->actingAs($this->user)
            ->visit($shelf->getUrl('/delete'))
            ->see('Delete Book');

        $this->setEntityRestrictions($shelf, ['view', 'update']);

        $this->forceVisit($shelf->getUrl('/delete'))
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($shelf, ['view', 'delete']);

        $this->visit($shelf->getUrl('/delete'))
            ->seePageIs($shelf->getUrl('/delete'))->see('Delete Book');
    }

    public function test_book_view_restriction()
    {
        $book = Book::first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)
            ->visit($bookUrl)
            ->seePageIs($bookUrl);

        $this->setEntityRestrictions($book, []);

        $this->forceVisit($bookUrl)
            ->see('Book not found');
        $this->forceVisit($bookPage->getUrl())
            ->see('Page not found');
        $this->forceVisit($bookChapter->getUrl())
            ->see('Chapter not found');

        $this->setEntityRestrictions($book, ['view']);

        $this->visit($bookUrl)
            ->see($book->name);
        $this->visit($bookPage->getUrl())
            ->see($bookPage->name);
        $this->visit($bookChapter->getUrl())
            ->see($bookChapter->name);
    }

    public function test_book_create_restriction()
    {
        $book = Book::first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->visit($bookUrl)
            ->dontSeeInElement('.actions', 'New Page')
            ->dontSeeInElement('.actions', 'New Chapter');
        $this->actingAs($this->user)
            ->visit($bookUrl)
            ->seeInElement('.actions', 'New Page')
            ->seeInElement('.actions', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'delete', 'update']);

        $this->forceVisit($bookUrl . '/create-chapter')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookUrl . '/create-page')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($bookUrl)->dontSeeInElement('.actions', 'New Page')
            ->dontSeeInElement('.actions', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'create']);

        $this->visit($bookUrl . '/create-chapter')
            ->type('test chapter', 'name')
            ->type('test description for chapter', 'description')
            ->press('Save Chapter')
            ->seePageIs($bookUrl . '/chapter/test-chapter');
        $this->visit($bookUrl . '/create-page')
            ->type('test page', 'name')
            ->type('test content', 'html')
            ->press('Save Page')
            ->seePageIs($bookUrl . '/page/test-page');
        $this->visit($bookUrl)->seeInElement('.actions', 'New Page')
            ->seeInElement('.actions', 'New Chapter');
    }

    public function test_book_update_restriction()
    {
        $book = Book::first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)
            ->visit($bookUrl . '/edit')
            ->see('Edit Book');

        $this->setEntityRestrictions($book, ['view', 'delete']);

        $this->forceVisit($bookUrl . '/edit')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookPage->getUrl() . '/edit')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookChapter->getUrl() . '/edit')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($book, ['view', 'update']);

        $this->visit($bookUrl . '/edit')
            ->seePageIs($bookUrl . '/edit');
        $this->visit($bookPage->getUrl() . '/edit')
            ->seePageIs($bookPage->getUrl() . '/edit');
        $this->visit($bookChapter->getUrl() . '/edit')
            ->see('Edit Chapter');
    }

    public function test_book_delete_restriction()
    {
        $book = Book::first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)
            ->visit($bookUrl . '/delete')
            ->see('Delete Book');

        $this->setEntityRestrictions($book, ['view', 'update']);

        $this->forceVisit($bookUrl . '/delete')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookPage->getUrl() . '/delete')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookChapter->getUrl() . '/delete')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($book, ['view', 'delete']);

        $this->visit($bookUrl . '/delete')
            ->seePageIs($bookUrl . '/delete')->see('Delete Book');
        $this->visit($bookPage->getUrl() . '/delete')
            ->seePageIs($bookPage->getUrl() . '/delete')->see('Delete Page');
        $this->visit($bookChapter->getUrl() . '/delete')
            ->see('Delete Chapter');
    }

    public function test_chapter_view_restriction()
    {
        $chapter = Chapter::first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->visit($chapterUrl)
            ->seePageIs($chapterUrl);

        $this->setEntityRestrictions($chapter, []);

        $this->forceVisit($chapterUrl)
            ->see('Chapter not found');
        $this->forceVisit($chapterPage->getUrl())
            ->see('Page not found');

        $this->setEntityRestrictions($chapter, ['view']);

        $this->visit($chapterUrl)
            ->see($chapter->name);
        $this->visit($chapterPage->getUrl())
            ->see($chapterPage->name);
    }

    public function test_chapter_create_restriction()
    {
        $chapter = Chapter::first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->visit($chapterUrl)
            ->seeInElement('.actions', 'New Page');

        $this->setEntityRestrictions($chapter, ['view', 'delete', 'update']);

        $this->forceVisit($chapterUrl . '/create-page')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($chapterUrl)->dontSeeInElement('.actions', 'New Page');

        $this->setEntityRestrictions($chapter, ['view', 'create']);


        $this->visit($chapterUrl . '/create-page')
            ->type('test page', 'name')
            ->type('test content', 'html')
            ->press('Save Page')
            ->seePageIs($chapter->book->getUrl() . '/page/test-page');

        $this->visit($chapterUrl)->seeInElement('.actions', 'New Page');
    }

    public function test_chapter_update_restriction()
    {
        $chapter = Chapter::first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->visit($chapterUrl . '/edit')
            ->see('Edit Chapter');

        $this->setEntityRestrictions($chapter, ['view', 'delete']);

        $this->forceVisit($chapterUrl . '/edit')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($chapterPage->getUrl() . '/edit')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($chapter, ['view', 'update']);

        $this->visit($chapterUrl . '/edit')
            ->seePageIs($chapterUrl . '/edit')->see('Edit Chapter');
        $this->visit($chapterPage->getUrl() . '/edit')
            ->seePageIs($chapterPage->getUrl() . '/edit');
    }

    public function test_chapter_delete_restriction()
    {
        $chapter = Chapter::first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->visit($chapterUrl . '/delete')
            ->see('Delete Chapter');

        $this->setEntityRestrictions($chapter, ['view', 'update']);

        $this->forceVisit($chapterUrl . '/delete')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($chapterPage->getUrl() . '/delete')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($chapter, ['view', 'delete']);

        $this->visit($chapterUrl . '/delete')
            ->seePageIs($chapterUrl . '/delete')->see('Delete Chapter');
        $this->visit($chapterPage->getUrl() . '/delete')
            ->seePageIs($chapterPage->getUrl() . '/delete')->see('Delete Page');
    }

    public function test_page_view_restriction()
    {
        $page = Page::first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->visit($pageUrl)
            ->seePageIs($pageUrl);

        $this->setEntityRestrictions($page, ['update', 'delete']);

        $this->forceVisit($pageUrl)
            ->see('Page not found');

        $this->setEntityRestrictions($page, ['view']);

        $this->visit($pageUrl)
            ->see($page->name);
    }

    public function test_page_update_restriction()
    {
        $page = Chapter::first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->visit($pageUrl . '/edit')
            ->seeInField('name', $page->name);

        $this->setEntityRestrictions($page, ['view', 'delete']);

        $this->forceVisit($pageUrl . '/edit')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($page, ['view', 'update']);

        $this->visit($pageUrl . '/edit')
            ->seePageIs($pageUrl . '/edit')->seeInField('name', $page->name);
    }

    public function test_page_delete_restriction()
    {
        $page = Page::first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->visit($pageUrl . '/delete')
            ->see('Delete Page');

        $this->setEntityRestrictions($page, ['view', 'update']);

        $this->forceVisit($pageUrl . '/delete')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($page, ['view', 'delete']);

        $this->visit($pageUrl . '/delete')
            ->seePageIs($pageUrl . '/delete')->see('Delete Page');
    }

    public function test_bookshelf_restriction_form()
    {
        $shelf = Bookshelf::first();
        $this->asAdmin()->visit($shelf->getUrl('/permissions'))
            ->see('Bookshelf Permissions')
            ->check('restricted')
            ->check('restrictions[2][view]')
            ->press('Save Permissions')
            ->seeInDatabase('bookshelves', ['id' => $shelf->id, 'restricted' => true])
            ->seeInDatabase('entity_permissions', [
                'restrictable_id' => $shelf->id,
                'restrictable_type' => Bookshelf::newModelInstance()->getMorphClass(),
                'role_id' => '2',
                'action' => 'view'
            ]);
    }

    public function test_book_restriction_form()
    {
        $book = Book::first();
        $this->asAdmin()->visit($book->getUrl() . '/permissions')
            ->see('Book Permissions')
            ->check('restricted')
            ->check('restrictions[2][view]')
            ->press('Save Permissions')
            ->seeInDatabase('books', ['id' => $book->id, 'restricted' => true])
            ->seeInDatabase('entity_permissions', [
                'restrictable_id' => $book->id,
                'restrictable_type' => Book::newModelInstance()->getMorphClass(),
                'role_id' => '2',
                'action' => 'view'
            ]);
    }

    public function test_chapter_restriction_form()
    {
        $chapter = Chapter::first();
        $this->asAdmin()->visit($chapter->getUrl() . '/permissions')
            ->see('Chapter Permissions')
            ->check('restricted')
            ->check('restrictions[2][update]')
            ->press('Save Permissions')
            ->seeInDatabase('chapters', ['id' => $chapter->id, 'restricted' => true])
            ->seeInDatabase('entity_permissions', [
                'restrictable_id' => $chapter->id,
                'restrictable_type' => Chapter::newModelInstance()->getMorphClass(),
                'role_id' => '2',
                'action' => 'update'
            ]);
    }

    public function test_page_restriction_form()
    {
        $page = Page::first();
        $this->asAdmin()->visit($page->getUrl() . '/permissions')
            ->see('Page Permissions')
            ->check('restricted')
            ->check('restrictions[2][delete]')
            ->press('Save Permissions')
            ->seeInDatabase('pages', ['id' => $page->id, 'restricted' => true])
            ->seeInDatabase('entity_permissions', [
                'restrictable_id' => $page->id,
                'restrictable_type' => Page::newModelInstance()->getMorphClass(),
                'role_id' => '2',
                'action' => 'delete'
            ]);
    }

    public function test_restricted_pages_not_visible_in_book_navigation_on_pages()
    {
        $chapter = Chapter::first();
        $page = $chapter->pages->first();
        $page2 = $chapter->pages[2];

        $this->setEntityRestrictions($page, []);

        $this->actingAs($this->user)
            ->visit($page2->getUrl())
            ->dontSeeInElement('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_in_book_navigation_on_chapters()
    {
        $chapter = Chapter::first();
        $page = $chapter->pages->first();

        $this->setEntityRestrictions($page, []);

        $this->actingAs($this->user)
            ->visit($chapter->getUrl())
            ->dontSeeInElement('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_on_chapter_pages()
    {
        $chapter = Chapter::first();
        $page = $chapter->pages->first();

        $this->setEntityRestrictions($page, []);

        $this->actingAs($this->user)
            ->visit($chapter->getUrl())
            ->dontSee($page->name);
    }

    public function test_restricted_chapter_pages_not_visible_on_book_page()
    {
        $chapter = Chapter::query()->first();
        $this->actingAs($this->user)
            ->visit($chapter->book->getUrl())
            ->see($chapter->pages->first()->name);

        foreach ($chapter->pages as $page) {
            $this->setEntityRestrictions($page, []);
        }

        $this->actingAs($this->user)
            ->visit($chapter->book->getUrl())
            ->dontSee($chapter->pages->first()->name);
    }

    public function test_bookshelf_update_restriction_override()
    {
        $shelf = Bookshelf::first();

        $this->actingAs($this->viewer)
            ->visit($shelf->getUrl('/edit'))
            ->dontSee('Edit Book');

        $this->setEntityRestrictions($shelf, ['view', 'delete']);

        $this->forceVisit($shelf->getUrl('/edit'))
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($shelf, ['view', 'update']);

        $this->visit($shelf->getUrl('/edit'))
            ->seePageIs($shelf->getUrl('/edit'));
    }

    public function test_bookshelf_delete_restriction_override()
    {
        $shelf = Bookshelf::first();

        $this->actingAs($this->viewer)
            ->visit($shelf->getUrl('/delete'))
            ->dontSee('Delete Book');

        $this->setEntityRestrictions($shelf, ['view', 'update']);

        $this->forceVisit($shelf->getUrl('/delete'))
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($shelf, ['view', 'delete']);

        $this->visit($shelf->getUrl('/delete'))
            ->seePageIs($shelf->getUrl('/delete'))->see('Delete Book');
    }

    public function test_book_create_restriction_override()
    {
        $book = Book::first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->visit($bookUrl)
            ->dontSeeInElement('.actions', 'New Page')
            ->dontSeeInElement('.actions', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'delete', 'update']);

        $this->forceVisit($bookUrl . '/create-chapter')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookUrl . '/create-page')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($bookUrl)->dontSeeInElement('.actions', 'New Page')
            ->dontSeeInElement('.actions', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'create']);

        $this->visit($bookUrl . '/create-chapter')
            ->type('test chapter', 'name')
            ->type('test description for chapter', 'description')
            ->press('Save Chapter')
            ->seePageIs($bookUrl . '/chapter/test-chapter');
        $this->visit($bookUrl . '/create-page')
            ->type('test page', 'name')
            ->type('test content', 'html')
            ->press('Save Page')
            ->seePageIs($bookUrl . '/page/test-page');
        $this->visit($bookUrl)->seeInElement('.actions', 'New Page')
            ->seeInElement('.actions', 'New Chapter');
    }

    public function test_book_update_restriction_override()
    {
        $book = Book::first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->visit($bookUrl . '/edit')
            ->dontSee('Edit Book');

        $this->setEntityRestrictions($book, ['view', 'delete']);

        $this->forceVisit($bookUrl . '/edit')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookPage->getUrl() . '/edit')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookChapter->getUrl() . '/edit')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($book, ['view', 'update']);

        $this->visit($bookUrl . '/edit')
            ->seePageIs($bookUrl . '/edit');
        $this->visit($bookPage->getUrl() . '/edit')
            ->seePageIs($bookPage->getUrl() . '/edit');
        $this->visit($bookChapter->getUrl() . '/edit')
            ->see('Edit Chapter');
    }

    public function test_book_delete_restriction_override()
    {
        $book = Book::first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->visit($bookUrl . '/delete')
            ->dontSee('Delete Book');

        $this->setEntityRestrictions($book, ['view', 'update']);

        $this->forceVisit($bookUrl . '/delete')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookPage->getUrl() . '/delete')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookChapter->getUrl() . '/delete')
            ->see('You do not have permission')->seePageIs('/');

        $this->setEntityRestrictions($book, ['view', 'delete']);

        $this->visit($bookUrl . '/delete')
            ->seePageIs($bookUrl . '/delete')->see('Delete Book');
        $this->visit($bookPage->getUrl() . '/delete')
            ->seePageIs($bookPage->getUrl() . '/delete')->see('Delete Page');
        $this->visit($bookChapter->getUrl() . '/delete')
            ->see('Delete Chapter');
    }

    public function test_page_visible_if_has_permissions_when_book_not_visible()
    {
        $book = Book::first();

        $this->setEntityRestrictions($book, []);

        $bookChapter = $book->chapters->first();
        $bookPage = $bookChapter->pages->first();
        $this->setEntityRestrictions($bookPage, ['view']);

        $this->actingAs($this->viewer);
        $this->get($bookPage->getUrl());
        $this->assertResponseOk();
        $this->see($bookPage->name);
        $this->dontSee(substr($book->name, 0, 15));
        $this->dontSee(substr($bookChapter->name, 0, 15));
    }

    public function test_book_sort_view_permission()
    {
        $firstBook = Book::first();
        $secondBook = Book::find(2);

        $this->setEntityRestrictions($firstBook, ['view', 'update']);
        $this->setEntityRestrictions($secondBook, ['view']);

        // Test sort page visibility
        $this->actingAs($this->user)->visit($secondBook->getUrl() . '/sort')
                ->see('You do not have permission')
                ->seePageIs('/');

        // Check sort page on first book
        $this->actingAs($this->user)->visit($firstBook->getUrl() . '/sort');
    }

    public function test_book_sort_permission() {
        $firstBook = Book::first();
        $secondBook = Book::find(2);

        $this->setEntityRestrictions($firstBook, ['view', 'update']);
        $this->setEntityRestrictions($secondBook, ['view']);

        $firstBookChapter = $this->newChapter(['name' => 'first book chapter'], $firstBook);
        $secondBookChapter = $this->newChapter(['name' => 'second book chapter'], $secondBook);

        // Create request data
        $reqData = [
            [
                'id' => $firstBookChapter->id,
                'sort' => 0,
                'parentChapter' => false,
                'type' => 'chapter',
                'book' => $secondBook->id
            ]
        ];

        // Move chapter from first book to a second book
        $this->actingAs($this->user)->put($firstBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)])
                ->followRedirects()
                ->see('You do not have permission')
                ->seePageIs('/');

        $reqData = [
            [
                'id' => $secondBookChapter->id,
                'sort' => 0,
                'parentChapter' => false,
                'type' => 'chapter',
                'book' => $firstBook->id
            ]
        ];

        // Move chapter from second book to first book
        $this->actingAs($this->user)->put($firstBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)])
                ->followRedirects()
                ->see('You do not have permission')
                ->seePageIs('/');
    }

    public function test_can_create_page_if_chapter_has_permissions_when_book_not_visible()
    {
        $book = Book::first();
        $this->setEntityRestrictions($book, []);
        $bookChapter = $book->chapters->first();
        $this->setEntityRestrictions($bookChapter, ['view']);

        $this->actingAs($this->user)->visit($bookChapter->getUrl())
            ->dontSee('New Page');

        $this->setEntityRestrictions($bookChapter, ['view', 'create']);

        $this->actingAs($this->user)->visit($bookChapter->getUrl())
            ->click('New Page')
            ->seeStatusCode(200)
            ->type('test page', 'name')
            ->type('test content', 'html')
            ->press('Save Page')
            ->seePageIs($book->getUrl('/page/test-page'))
            ->seeStatusCode(200);
    }
}
