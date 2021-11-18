<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
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

    protected function setUp(): void
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

    public function test_draft_save_shows_alert_if_draft_older_than_last_page_update()
    {
        $admin = $this->getAdmin();
        $editor = $this->getEditor();
        /** @var Page $page */
        $page = Page::query()->first();

        $this->actingAs($editor)->put('/ajax/page/' . $page->id . '/save-draft', [
            'name' => $page->name,
            'html' => '<p>updated draft</p>',
        ]);

        /** @var PageRevision $draft */
        $draft = $page->allRevisions()
            ->where('type', '=', 'update_draft')
            ->where('created_by', '=', $editor->id)
            ->first();
        $draft->created_at = now()->subMinute(1);
        $draft->save();

        $this->actingAs($admin)->put($page->refresh()->getUrl(), [
            'name' => $page->name,
            'html' => '<p>admin update</p>',
        ]);

        $resp = $this->actingAs($editor)->put('/ajax/page/' . $page->id . '/save-draft', [
            'name' => $page->name,
            'html' => '<p>updated draft again</p>',
        ]);

        $resp->assertJson([
            'warning' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
        ]);
    }

    public function test_draft_save_shows_alert_if_draft_edit_started_by_someone_else()
    {
        $admin = $this->getAdmin();
        $editor = $this->getEditor();
        /** @var Page $page */
        $page = Page::query()->first();

        $this->actingAs($admin)->put('/ajax/page/' . $page->id . '/save-draft', [
            'name' => $page->name,
            'html' => '<p>updated draft</p>',
        ]);

        $resp = $this->actingAs($editor)->put('/ajax/page/' . $page->id . '/save-draft', [
            'name' => $page->name,
            'html' => '<p>updated draft again</p>',
        ]);

        $resp->assertJson([
            'warning' => 'Admin has started editing this page in the last 60 minutes. Take care not to overwrite each other\'s updates!',
        ]);
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

    public function test_updating_page_draft_with_markdown_retains_markdown_content()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $this->asEditor()->get($book->getUrl('/create-page'));
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->where('book_id', '=', $book->id)->firstOrFail();

        $resp = $this->put('/ajax/page/' . $draft->id . '/save-draft', [
            'name'     => 'My updated draft',
            'markdown' => "# My markdown page\n\n[A link](https://example.com)",
            'html'     => '<p>checking markdown takes priority over this</p>',
        ]);
        $resp->assertOk();

        $this->assertDatabaseHas('pages', [
            'id'       => $draft->id,
            'draft'    => true,
            'name'     => 'My updated draft',
            'markdown' => "# My markdown page\n\n[A link](https://example.com)",
        ]);

        $draft->refresh();
        $this->assertStringContainsString('href="https://example.com"', $draft->html);
    }
}
