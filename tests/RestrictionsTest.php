<?php

class RestrictionsTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->getNewUser();
    }

    /**
     * Manually set some restrictions on an entity.
     * @param \BookStack\Entity $entity
     * @param $actions
     */
    protected function setEntityRestrictions(\BookStack\Entity $entity, $actions)
    {
        $entity->restricted = true;
        $entity->restrictions()->delete();
        $role = $this->user->roles->first();
        foreach ($actions as $action) {
            $entity->restrictions()->create([
                'role_id' => $role->id,
                'action' => strtolower($action)
            ]);
        }
        $entity->save();
        $entity->load('restrictions');
    }

    public function test_book_view_restriction()
    {
        $book = \BookStack\Book::first();
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
            ->see('Book not found');
        $this->forceVisit($bookChapter->getUrl())
            ->see('Book not found');

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
        $book = \BookStack\Book::first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)
            ->visit($bookUrl)
            ->seeInElement('.action-buttons', 'New Page')
            ->seeInElement('.action-buttons', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'delete', 'update']);

        $this->forceVisit($bookUrl . '/chapter/create')
            ->see('You do not have permission')->seePageIs('/');
        $this->forceVisit($bookUrl . '/page/create')
            ->see('You do not have permission')->seePageIs('/');
        $this->visit($bookUrl)->dontSeeInElement('.action-buttons', 'New Page')
            ->dontSeeInElement('.action-buttons', 'New Chapter');

        $this->setEntityRestrictions($book, ['view', 'create']);

        $this->visit($bookUrl . '/chapter/create')
            ->type('test chapter', 'name')
            ->type('test description for chapter', 'description')
            ->press('Save Chapter')
            ->seePageIs($bookUrl . '/chapter/test-chapter');
        $this->visit($bookUrl . '/page/create')
            ->type('test page', 'name')
            ->type('test content', 'html')
            ->press('Save Page')
            ->seePageIs($bookUrl . '/page/test-page');
        $this->visit($bookUrl)->seeInElement('.action-buttons', 'New Page')
            ->seeInElement('.action-buttons', 'New Chapter');
    }

    public function test_book_update_restriction()
    {
        $book = \BookStack\Book::first();
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
        $book = \BookStack\Book::first();
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
        $book = \BookStack\Book::first();
        $this->asAdmin()->visit($book->getUrl() . '/restrict')
            ->see('Book Restrictions')
            ->check('restricted')
            ->check('restrictions[2][view]')
            ->press('Save Restrictions')
            ->seeInDatabase('books', ['id' => $book->id, 'restricted' => true])
            ->seeInDatabase('restrictions', [
                'restrictable_id' => $book->id,
                'restrictable_type' => 'BookStack\Book',
                'role_id' => '2',
                'action' => 'view'
            ]);
    }

    public function test_chapter_restriction_form()
    {
        $chapter = \BookStack\Chapter::first();
        $this->asAdmin()->visit($chapter->getUrl() . '/restrict')
            ->see('Chapter Restrictions')
            ->check('restricted')
            ->check('restrictions[2][update]')
            ->press('Save Restrictions')
            ->seeInDatabase('chapters', ['id' => $chapter->id, 'restricted' => true])
            ->seeInDatabase('restrictions', [
                'restrictable_id' => $chapter->id,
                'restrictable_type' => 'BookStack\Chapter',
                'role_id' => '2',
                'action' => 'update'
            ]);
    }

    public function test_page_restriction_form()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin()->visit($page->getUrl() . '/restrict')
            ->see('Page Restrictions')
            ->check('restricted')
            ->check('restrictions[2][delete]')
            ->press('Save Restrictions')
            ->seeInDatabase('pages', ['id' => $page->id, 'restricted' => true])
            ->seeInDatabase('restrictions', [
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

}
