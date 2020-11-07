<?php namespace Tests\Entity;

use BookStack\Entities\Chapter;
use Tests\TestCase;

class ChapterTest extends TestCase
{
    public function test_chapter_delete()
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
        $redirectReq->assertNotificationContains('Chapter Successfully Deleted');
    }
}