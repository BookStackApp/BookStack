<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use Tests\TestCase;

class PageDraftTest extends TestCase
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var PageRepo
     */
    protected $pageRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->page = Page::query()->first();
        $this->pageRepo = app()->make(PageRepo::class);
    }

    public function test_draft_content_shows_if_available()
    {
        $addedContent = '<p>test message content</p>';

        $this->asAdmin()->get($this->page->getUrl('/edit'))
            ->assertElementNotContains('[name="html"]', $addedContent);

        $newContent = $this->page->html . $addedContent;
        $this->pageRepo->updatePageDraft($this->page, ['html' => $newContent]);
        $this->asAdmin()->get($this->page->getUrl('/edit'))
            ->assertElementContains('[name="html"]', $newContent);
    }

    public function test_draft_not_visible_by_others()
    {
        $addedContent = '<p>test message content</p>';
        $this->asAdmin()->get($this->page->getUrl('/edit'))
            ->assertElementNotContains('[name="html"]', $addedContent);

        $newContent = $this->page->html . $addedContent;
        $newUser = $this->getEditor();
        $this->pageRepo->updatePageDraft($this->page, ['html' => $newContent]);

        $this->actingAs($newUser)->get($this->page->getUrl('/edit'))
            ->assertElementNotContains('[name="html"]', $newContent);
    }

    public function test_alert_message_shows_if_editing_draft()
    {
        $this->asAdmin();
        $this->pageRepo->updatePageDraft($this->page, ['html' => 'test content']);
        $this->asAdmin()->get($this->page->getUrl('/edit'))
            ->assertSee('You are currently editing a draft');
    }

    public function test_alert_message_shows_if_someone_else_editing()
    {
        $nonEditedPage = Page::query()->take(10)->get()->last();
        $addedContent = '<p>test message content</p>';
        $this->asAdmin()->get($this->page->getUrl('/edit'))
            ->assertElementNotContains('[name="html"]', $addedContent);

        $newContent = $this->page->html . $addedContent;
        $newUser = $this->getEditor();
        $this->pageRepo->updatePageDraft($this->page, ['html' => $newContent]);

        $this->actingAs($newUser)
            ->get($this->page->getUrl('/edit'))
            ->assertSee('Admin has started editing this page');
        $this->flushSession();
        $this->get($nonEditedPage->getUrl() . '/edit')
            ->assertElementNotContains('.notification', 'Admin has started editing this page');
    }

    public function test_draft_pages_show_on_homepage()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $this->asAdmin()->get('/')
            ->assertElementNotContains('#recent-drafts', 'New Page');

        $this->get($book->getUrl() . '/create-page');

        $this->get('/')->assertElementContains('#recent-drafts', 'New Page');
    }

    public function test_draft_pages_not_visible_by_others()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $chapter = $book->chapters->first();
        $newUser = $this->getEditor();

        $this->actingAs($newUser)->get($book->getUrl('/create-page'));
        $this->get($chapter->getUrl('/create-page'));
        $this->get($book->getUrl())
            ->assertElementContains('.book-contents', 'New Page');

        $this->asAdmin()->get($book->getUrl())
            ->assertElementNotContains('.book-contents', 'New Page');
        $this->get($chapter->getUrl())
            ->assertElementNotContains('.book-contents', 'New Page');
    }

    public function test_page_html_in_ajax_fetch_response()
    {
        $this->asAdmin();
        /** @var Page $page */
        $page = Page::query()->first();

        $this->getJson('/ajax/page/' . $page->id)->assertJson([
            'html' => $page->html,
        ]);
    }
}
