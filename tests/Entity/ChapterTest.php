<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class ChapterTest extends TestCase
{
    public function test_create()
    {
        $book = $this->entities->book();

        $chapter = Chapter::factory()->make([
            'name' => 'My First Chapter',
        ]);

        $resp = $this->asEditor()->get($book->getUrl());
        $this->withHtml($resp)->assertElementContains('a[href="' . $book->getUrl('/create-chapter') . '"]', 'New Chapter');

        $resp = $this->get($book->getUrl('/create-chapter'));
        $this->withHtml($resp)->assertElementContains('form[action="' . $book->getUrl('/create-chapter') . '"][method="POST"]', 'Save Chapter');

        $resp = $this->post($book->getUrl('/create-chapter'), $chapter->only('name', 'description_html'));
        $resp->assertRedirect($book->getUrl('/chapter/my-first-chapter'));

        $resp = $this->get($book->getUrl('/chapter/my-first-chapter'));
        $resp->assertSee($chapter->name);
        $resp->assertSee($chapter->description_html, false);
    }

    public function test_show_view_displays_description_if_no_description_html_set()
    {
        $chapter = $this->entities->chapter();
        $chapter->description_html = '';
        $chapter->description = "My great\ndescription\n\nwith newlines";
        $chapter->save();

        $resp = $this->asEditor()->get($chapter->getUrl());
        $resp->assertSee("<p>My great<br>\ndescription<br>\n<br>\nwith newlines</p>", false);
    }

    public function test_delete()
    {
        $chapter = Chapter::query()->whereHas('pages')->first();
        $this->assertNull($chapter->deleted_at);
        $pageCount = $chapter->pages()->count();

        $deleteViewReq = $this->asEditor()->get($chapter->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this chapter?');

        $deleteReq = $this->delete($chapter->getUrl());
        $deleteReq->assertRedirect($chapter->getParent()->getUrl());
        $this->assertActivityExists('chapter_delete', $chapter);

        $chapter->refresh();
        $this->assertNotNull($chapter->deleted_at);

        $this->assertTrue($chapter->pages()->count() === 0);
        $this->assertTrue($chapter->pages()->withTrashed()->count() === $pageCount);
        $this->assertTrue($chapter->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $this->assertNotificationContains($redirectReq, 'Chapter Successfully Deleted');
    }



    public function test_sort_book_action_visible_if_permissions_allow()
    {
        $chapter = $this->entities->chapter();

        $resp = $this->actingAs($this->users->viewer())->get($chapter->getUrl());
        $this->withHtml($resp)->assertLinkNotExists($chapter->book->getUrl('sort'));

        $resp = $this->asEditor()->get($chapter->getUrl());
        $this->withHtml($resp)->assertLinkExists($chapter->book->getUrl('sort'));
    }
}
