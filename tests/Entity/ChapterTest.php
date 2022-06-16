<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use DOMXPath;
use Tests\TestCase;

class ChapterTest extends TestCase
{
    public function test_create()
    {
        /** @var Book $book */
        $book = Book::query()->first();

        $chapter = Chapter::factory()->make([
            'name' => 'My First Chapter',
        ]);

        $resp = $this->asEditor()->get($book->getUrl());
        $resp->assertElementContains('a[href="' . $book->getUrl('/create-chapter') . '"]', 'New Chapter');

        $resp = $this->get($book->getUrl('/create-chapter'));
        $resp->assertElementContains('form[action="' . $book->getUrl('/create-chapter') . '"][method="POST"]', 'Save Chapter');

        $resp = $this->post($book->getUrl('/create-chapter'), $chapter->only('name', 'description'));
        $resp->assertRedirect($book->getUrl('/chapter/my-first-chapter'));

        $resp = $this->get($book->getUrl('/chapter/my-first-chapter'));
        $resp->assertSee($chapter->name);
        $resp->assertSee($chapter->description);
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
        $redirectReq->assertNotificationContains('Chapter Successfully Deleted');
    }

    public function test_show_view_has_copy_button()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();

        $resp = $this->asEditor()->get($chapter->getUrl());
        $resp->assertElementContains("a[href$=\"{$chapter->getUrl('/copy')}\"]", 'Copy');
    }

    public function test_copy_view()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();

        $resp = $this->asEditor()->get($chapter->getUrl('/copy'));
        $resp->assertOk();
        $resp->assertSee('Copy Chapter');
        $resp->assertElementExists("input[name=\"name\"][value=\"{$chapter->name}\"]");
        $resp->assertElementExists('input[name="entity_selection"]');
    }

    public function test_copy()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        /** @var Book $otherBook */
        $otherBook = Book::query()->where('id', '!=', $chapter->book_id)->first();

        $resp = $this->asEditor()->post($chapter->getUrl('/copy'), [
            'name'             => 'My copied chapter',
            'entity_selection' => 'book:' . $otherBook->id,
        ]);

        /** @var Chapter $newChapter */
        $newChapter = Chapter::query()->where('name', '=', 'My copied chapter')->first();

        $resp->assertRedirect($newChapter->getUrl());
        $this->assertEquals($otherBook->id, $newChapter->book_id);
        $this->assertEquals($chapter->pages->count(), $newChapter->pages->count());
    }

    public function test_copy_does_not_copy_non_visible_pages()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();

        // Hide pages to all non-admin roles
        /** @var Page $page */
        foreach ($chapter->pages as $page) {
            $page->restricted = true;
            $page->save();
            $this->regenEntityPermissions($page);
        }

        $this->asEditor()->post($chapter->getUrl('/copy'), [
            'name' => 'My copied chapter',
        ]);

        /** @var Chapter $newChapter */
        $newChapter = Chapter::query()->where('name', '=', 'My copied chapter')->first();
        $this->assertEquals(0, $newChapter->pages()->count());
    }

    public function test_copy_does_not_copy_pages_if_user_cant_page_create()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        $viewer = $this->getViewer();
        $this->giveUserPermissions($viewer, ['chapter-create-all']);

        // Lacking permission results in no copied pages
        $this->actingAs($viewer)->post($chapter->getUrl('/copy'), [
            'name' => 'My copied chapter',
        ]);

        /** @var Chapter $newChapter */
        $newChapter = Chapter::query()->where('name', '=', 'My copied chapter')->first();
        $this->assertEquals(0, $newChapter->pages()->count());

        $this->giveUserPermissions($viewer, ['page-create-all']);

        // Having permission rules in copied pages
        $this->actingAs($viewer)->post($chapter->getUrl('/copy'), [
            'name' => 'My copied again chapter',
        ]);

        /** @var Chapter $newChapter2 */
        $newChapter2 = Chapter::query()->where('name', '=', 'My copied again chapter')->first();
        $this->assertEquals($chapter->pages()->count(), $newChapter2->pages()->count());
    }

    /**
     * Test copy chapter having pages with html containing links pointing to pages with the same chapter as parent.
     * Make sure we update the links so they points to the new cloned pages (which will have the new cloned chapter
     * as parent)
     */
    public function test_copy_does_update_html_self_referentials_child_page_links()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        $pages = $chapter->pages->all();
        $pageCount = count($pages);

        $previousPage = $pages[$pageCount - 1];
        $previousPage->name = $pageCount - 1;
        $previousPage->refreshSlug();
        $previousPage->save();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $page->name = $i;   
            $page->html = "<p><a title='Some link' href='{$previousPage->getUrl()}'>some serious test content</a></p>";
            $previousPage = $page;
            $page->refreshSlug();
            $page->save();
        }

        $this->asEditor()->post($chapter->getUrl('/copy'), [
            'name' => 'My copied chapter wxcd',
        ]);

        /** @var Chapter $copiedChapter */
        $copiedChapter = Chapter::query()->where('name', '=', 'My copied chapter wxcd')->first();
        $copiedPages = $copiedChapter->pages->sortBy('name')->all();

        $previousPage = $copiedPages[$pageCount - 1];

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $copiedPages[$i];
            $this->assertEquals("<p id=\"bkmrk-some-serious-test-co\"><a title='Some link' href='{$previousPage->getUrl()}'>some serious test content</a></p>", $page->html);
            $previousPage = $page;
        }
    }

    /** @see Tests\Entity\ChapterTest::test_copy_does_update_html_self_referentials_child_page_links() */
    public function test_copy_does_update_markdown_self_referentials_child_page_links()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        $firstPage = $chapter->pages[0];
        $secondPage = $chapter->pages[1];

        $firstPage->markdown = "[Awesome related page]({$secondPage->getUrl()})";
        $secondPage->markdown = "[Awesome related page]({$firstPage->getUrl()})";
        $firstPage->save();
        $secondPage->save();

        $this->asEditor()->post($chapter->getUrl('/copy'), [
            'name' => 'My copied chapter wxcd',
        ]);

        /** @var Chapter $copiedChapter */
        $copiedChapter = Chapter::query()->where('name', '=', 'My copied chapter wxcd')->first();

        $copiedFirstPage = $copiedChapter->pages[0];
        $copiedSecondPage = $copiedChapter->pages[1];

        $this->assertEquals($copiedFirstPage->markdown, "[Awesome related page]({$copiedSecondPage->getUrl()})");
        $this->assertEquals($copiedSecondPage->markdown, "[Awesome related page]({$copiedFirstPage->getUrl()})");
    }

    /**
     * Test chapter copy having pages with content that does not contains any self-referential links.
     * Copy should not alter those links.
     */
    public function test_copy_does_not_update_html_external_page_links()
    {
        // todo
        $this->assertTrue(true);
    }

    /** @see \Tests\Entity\ChapterTest::test_copy_does_not_update_html_external_page_links() */
    public function test_copy_does_not_update_markdown_external_page_links()
    {
        // todo
        $this->assertTrue(true);
    }

    /**
     * Test copy chapter having pages with html containing links pointing to the very same chapter.
     * Make sure we update the links so they points to the new cloned chapter.
     */
    public function test_copy_does_update_html_self_referentials_links()
    {
        $this->assertTrue(true);
    }

    /** @see \Tests\Entity\ChapterTest::test_copy_does_update_html_self_referentials_links() */
    public function test_copy_does_update_markdown_self_referentials_links()
    {
        $this->assertTrue(true);
    }
}
