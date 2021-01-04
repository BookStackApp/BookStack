<?php namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PageTest extends TestCase
{
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
}