<?php namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PageTest extends TestCase
{

    public function test_page_view_when_creator_is_deleted_but_owner_exists()
    {
        $page = Page::query()->first();
        $user = $this->getViewer();
        $owner = $this->getEditor();
        $page->created_by = $user->id;
        $page->owned_by = $owner->id;
        $page->save();
        $user->delete();

        $resp = $this->asAdmin()->get($page->getUrl());
        $resp->assertStatus(200);
        $resp->assertSeeText('Owned by ' . $owner->name);
    }

    public function test_page_creation_with_markdown_content()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        $book = Book::query()->first();

        $this->asEditor()->get($book->getUrl('/create-page'));
        $draft = Page::query()->where('book_id', '=', $book->id)
            ->where('draft', '=', true)->first();

        $details = [
            'markdown' => '# a title',
            'html' => '<h1>a title</h1>',
            'name' => 'my page',
        ];
        $resp = $this->post($book->getUrl("/draft/{$draft->id}"), $details);
        $resp->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'markdown' => $details['markdown'],
            'name' => $details['name'],
            'id' => $draft->id,
            'draft' => false
        ]);

        $draft->refresh();
        $resp = $this->get($draft->getUrl("/edit"));
        $resp->assertSee("# a title");
    }

    public function test_page_delete()
    {
        $page = Page::query()->first();
        $this->assertNull($page->deleted_at);

        $deleteViewReq = $this->asEditor()->get($page->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this page?');

        $deleteReq = $this->delete($page->getUrl());
        $deleteReq->assertRedirect($page->getParent()->getUrl());
        $this->assertActivityExists('page_delete', $page);

        $page->refresh();
        $this->assertNotNull($page->deleted_at);
        $this->assertTrue($page->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $redirectReq->assertNotificationContains('Page Successfully Deleted');
    }

    public function test_page_copy()
    {
        $page = Page::first();
        $page->html = '<p>This is some test content</p>';
        $page->save();

        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $resp = $this->asEditor()->get($page->getUrl('/copy'));
        $resp->assertSee('Copy Page');

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name' => 'My copied test page'
        ]);
        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $movePageResp->assertRedirect($pageCopy->getUrl());
        $this->assertTrue($pageCopy->book->id == $newBook->id, 'Page was copied to correct book');
        $this->assertStringContainsString('This is some test content', $pageCopy->html);
    }

    public function test_page_copy_with_markdown_has_both_html_and_markdown()
    {
        $page = Page::first();
        $page->html = '<h1>This is some test content</h1>';
        $page->markdown = '# This is some test content';
        $page->save();
        $newBook = Book::where('id', '!=', $page->book->id)->first();

        $this->asEditor()->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name' => 'My copied test page'
        ]);
        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $this->assertStringContainsString('This is some test content', $pageCopy->html);
        $this->assertEquals('# This is some test content', $pageCopy->markdown);
    }

    public function test_page_copy_with_no_destination()
    {
        $page = Page::first();
        $currentBook = $page->book;

        $resp = $this->asEditor()->get($page->getUrl('/copy'));
        $resp->assertSee('Copy Page');

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'name' => 'My copied test page'
        ]);

        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $movePageResp->assertRedirect($pageCopy->getUrl());
        $this->assertTrue($pageCopy->book->id == $currentBook->id, 'Page was copied to correct book');
        $this->assertTrue($pageCopy->id !== $page->id, 'Page copy is not the same instance');
    }

    public function test_page_can_be_copied_without_edit_permission()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $viewer = $this->getViewer();

        $resp = $this->actingAs($viewer)->get($page->getUrl());
        $resp->assertDontSee($page->getUrl('/copy'));

        $newBook->owned_by = $viewer->id;
        $newBook->save();
        $this->giveUserPermissions($viewer, ['page-create-own']);
        $this->regenEntityPermissions($newBook);

        $resp = $this->actingAs($viewer)->get($page->getUrl());
        $resp->assertSee($page->getUrl('/copy'));

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name' => 'My copied test page'
        ]);
        $movePageResp->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'name' => 'My copied test page',
            'created_by' => $viewer->id,
            'book_id' => $newBook->id,
        ]);
    }

    public function test_empty_markdown_still_saves_without_error()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        $book = Book::query()->first();

        $this->asEditor()->get($book->getUrl('/create-page'));
        $draft = Page::query()->where('book_id', '=', $book->id)
            ->where('draft', '=', true)->first();

        $details = [
            'name' => 'my page',
            'markdown' => '',
        ];
        $resp = $this->post($book->getUrl("/draft/{$draft->id}"), $details);
        $resp->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'markdown' => $details['markdown'],
            'id' => $draft->id,
            'draft' => false
        ]);
    }
}