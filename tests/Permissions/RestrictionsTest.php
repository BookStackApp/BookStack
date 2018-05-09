<?php namespace Tests;

use BookStack\Book;
use BookStack\Entity;
use BookStack\User;
use BookStack\Repos\EntityRepo;

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

    public function setUp()
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
            ->dontSeeInElement('.action-buttons', 'New Page')
            ->dontSeeInElement('.action-buttons', 'New Chapter');
        $this->actingAs($this->user)
            ->visit($bookUrl)
            ->seeInElement('.action-buttons', 'New Page')
            ->seeInElement('.action-buttons', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'delete', 'update']);

        $this->forceVisit($bookUrl . '/create-chapter')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookUrl . '/create-page')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($bookUrl)->dontSeeInElement('.action-buttons', 'New Page')
            ->dontSeeInElement('.action-buttons', 'New Chapter');

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
        $this->visit($bookUrl)->seeInElement('.action-buttons', 'New Page')
            ->seeInElement('.action-buttons', 'New Chapter');
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
        $chapter = \BookStack\Chapter::first();
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
        $chapter = \BookStack\Chapter::first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->visit($chapterUrl)
            ->seeInElement('.action-buttons', 'New Page');

        $this->setEntityRestrictions($chapter, ['view', 'delete', 'update']);

        $this->forceVisit($chapterUrl . '/create-page')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($chapterUrl)->dontSeeInElement('.action-buttons', 'New Page');

        $this->setEntityRestrictions($chapter, ['view', 'create']);


        $this->visit($chapterUrl . '/create-page')
            ->type('test page', 'name')
            ->type('test content', 'html')
            ->press('Save Page')
            ->seePageIs($chapter->book->getUrl() . '/page/test-page');

        $this->visit($chapterUrl)->seeInElement('.action-buttons', 'New Page');
    }

    public function test_chapter_update_restriction()
    {
        $chapter = \BookStack\Chapter::first();
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
        $chapter = \BookStack\Chapter::first();
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
        $page = \BookStack\Page::first();

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
        $page = \BookStack\Chapter::first();

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
        $page = \BookStack\Page::first();

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
                'restrictable_type' => 'BookStack\Book',
                'role_id' => '2',
                'action' => 'view'
            ]);
    }

    public function test_chapter_restriction_form()
    {
        $chapter = \BookStack\Chapter::first();
        $this->asAdmin()->visit($chapter->getUrl() . '/permissions')
            ->see('Chapter Permissions')
            ->check('restricted')
            ->check('restrictions[2][update]')
            ->press('Save Permissions')
            ->seeInDatabase('chapters', ['id' => $chapter->id, 'restricted' => true])
            ->seeInDatabase('entity_permissions', [
                'restrictable_id' => $chapter->id,
                'restrictable_type' => 'BookStack\Chapter',
                'role_id' => '2',
                'action' => 'update'
            ]);
    }

    public function test_page_restriction_form()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin()->visit($page->getUrl() . '/permissions')
            ->see('Page Permissions')
            ->check('restricted')
            ->check('restrictions[2][delete]')
            ->press('Save Permissions')
            ->seeInDatabase('pages', ['id' => $page->id, 'restricted' => true])
            ->seeInDatabase('entity_permissions', [
                'restrictable_id' => $page->id,
                'restrictable_type' => 'BookStack\Page',
                'role_id' => '2',
                'action' => 'delete'
            ]);
    }

    public function test_restricted_pages_not_visible_in_book_navigation_on_pages()
    {
        $chapter = \BookStack\Chapter::first();
        $page = $chapter->pages->first();
        $page2 = $chapter->pages[2];

        $this->setEntityRestrictions($page, []);

        $this->actingAs($this->user)
            ->visit($page2->getUrl())
            ->dontSeeInElement('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_in_book_navigation_on_chapters()
    {
        $chapter = \BookStack\Chapter::first();
        $page = $chapter->pages->first();

        $this->setEntityRestrictions($page, []);

        $this->actingAs($this->user)
            ->visit($chapter->getUrl())
            ->dontSeeInElement('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_on_chapter_pages()
    {
        $chapter = \BookStack\Chapter::first();
        $page = $chapter->pages->first();

        $this->setEntityRestrictions($page, []);

        $this->actingAs($this->user)
            ->visit($chapter->getUrl())
            ->dontSee($page->name);
    }

    public function test_book_create_restriction_override()
    {
        $book = Book::first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->visit($bookUrl)
            ->dontSeeInElement('.action-buttons', 'New Page')
            ->dontSeeInElement('.action-buttons', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'delete', 'update']);

        $this->forceVisit($bookUrl . '/create-chapter')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookUrl . '/create-page')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($bookUrl)->dontSeeInElement('.action-buttons', 'New Page')
            ->dontSeeInElement('.action-buttons', 'New Chapter');

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
        $this->visit($bookUrl)->seeInElement('.action-buttons', 'New Page')
            ->seeInElement('.action-buttons', 'New Chapter');
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
        $thirdBook = Book::find(3);

        $this->setEntityRestrictions($firstBook, ['view', 'update']);
        $this->setEntityRestrictions($secondBook, ['view']);
        $this->setEntityRestrictions($thirdBook, ['view', 'update']);

        // Test sort page visibility
        $this->actingAs($this->user)->visit($secondBook->getUrl() . '/sort')
                ->see('You do not have permission')
                ->seePageIs('/');

        // Check sort page on first book
        $this->actingAs($this->user)->visit($firstBook->getUrl() . '/sort')
                ->see($thirdBook->name)
                ->dontSee($secondBook->name);
    }

    public function test_book_sort_permission() {
        $firstBook = Book::first();
        $secondBook = Book::find(2);

        $this->setEntityRestrictions($firstBook, ['view', 'update']);
        $this->setEntityRestrictions($secondBook, ['view']);

        $firstBookChapter = $this->app[EntityRepo::class]->createFromInput('chapter',
                ['name' => 'first book chapter'], $firstBook);
        $secondBookChapter = $this->app[EntityRepo::class]->createFromInput('chapter',
                ['name' => 'second book chapter'], $secondBook);

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
}
