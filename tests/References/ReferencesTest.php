<?php

namespace Tests\References;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Model;
use BookStack\References\Reference;
use Tests\TestCase;

class ReferencesTest extends TestCase
{
    public function test_references_created_on_page_update()
    {
        /** @var Page $pageA */
        /** @var Page $pageB */
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();

        $this->assertDatabaseMissing('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);

        $this->asEditor()->put($pageA->getUrl(), [
            'name' => 'Reference test',
            'html' => '<a href="' . $pageB->getUrl() . '">Testing</a>',
        ]);

        $this->assertDatabaseHas('references', [
            'from_id'   => $pageA->id,
            'from_type' => $pageA->getMorphClass(),
            'to_id'     => $pageB->id,
            'to_type'   => $pageB->getMorphClass(),
        ]);
    }

    public function test_references_deleted_on_entity_delete()
    {
        /** @var Page $pageA */
        /** @var Page $pageB */
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();

        $this->createReference($pageA, $pageB);
        $this->createReference($pageB, $pageA);

        $this->assertDatabaseHas('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);
        $this->assertDatabaseHas('references', ['to_id' => $pageA->id, 'to_type' => $pageA->getMorphClass()]);

        app(PageRepo::class)->destroy($pageA);
        app(TrashCan::class)->empty();

        $this->assertDatabaseMissing('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);
        $this->assertDatabaseMissing('references', ['to_id' => $pageA->id, 'to_type' => $pageA->getMorphClass()]);
    }

    public function test_references_to_count_visible_on_entity_show_view()
    {
        $entities = $this->getEachEntityType();
        /** @var Page $otherPage */
        $otherPage = Page::query()->where('id', '!=', $entities['page']->id)->first();

        $this->asEditor();
        foreach ($entities as $entity) {
            $this->createReference($entities['page'], $entity);
        }

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $resp->assertSee('Referenced on 1 page');
            $resp->assertDontSee('Referenced on 1 pages');
        }

        $this->createReference($otherPage, $entities['page']);
        $resp = $this->get($entities['page']->getUrl());
        $resp->assertSee('Referenced on 2 pages');
    }

    public function test_references_to_visible_on_references_page()
    {
        $entities = $this->getEachEntityType();
        $this->asEditor();
        foreach ($entities as $entity) {
            $this->createReference($entities['page'], $entity);
        }

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl('/references'));
            $resp->assertSee('References');
            $resp->assertSee($entities['page']->name);
            $resp->assertDontSee('There are no tracked references');
        }
    }

    public function test_reference_not_visible_if_view_permission_does_not_permit()
    {
        /** @var Page $page */
        /** @var Page $pageB */
        $page = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $page->id)->first();
        $this->createReference($pageB, $page);

        $this->setEntityRestrictions($pageB);

        $this->asEditor()->get($page->getUrl('/references'))->assertDontSee($pageB->name);
        $this->asAdmin()->get($page->getUrl('/references'))->assertSee($pageB->name);
    }

    public function test_reference_page_shows_empty_state_with_no_references()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $this->asEditor()
            ->get($page->getUrl('/references'))
            ->assertSee('There are no tracked references');
    }

    public function test_pages_leading_to_entity_updated_on_url_change()
    {
        /** @var Page $pageA */
        /** @var Page $pageB */
        /** @var Book $book */
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();
        $book = Book::query()->first();

        foreach ([$pageA, $pageB] as $page) {
            $page->html = '<a href="' . $book->getUrl() . '">Link</a>';
            $page->save();
            $this->createReference($page, $book);
        }

        $this->asEditor()->put($book->getUrl(), [
            'name' => 'my updated book slugaroo',
        ]);

        foreach ([$pageA, $pageB] as $page) {
            $page->refresh();
            $this->assertStringContainsString('href="http://localhost/books/my-updated-book-slugaroo"', $page->html);
            $this->assertDatabaseHas('page_revisions', [
                'page_id' => $page->id,
                'summary' => 'System auto-update of internal links',
            ]);
        }
    }

    public function test_pages_linking_to_other_page_updated_on_parent_book_url_change()
    {
        /** @var Page $bookPage */
        /** @var Page $otherPage */
        /** @var Book $book */
        $bookPage = Page::query()->first();
        $otherPage = Page::query()->where('id', '!=', $bookPage->id)->first();
        $book = $bookPage->book;

        $otherPage->html = '<a href="' . $bookPage->getUrl() . '">Link</a>';
        $otherPage->save();
        $this->createReference($otherPage, $bookPage);

        $this->asEditor()->put($book->getUrl(), [
            'name' => 'my updated book slugaroo',
        ]);

        $otherPage->refresh();
        $this->assertStringContainsString('href="http://localhost/books/my-updated-book-slugaroo/page/' . $bookPage->slug . '"', $otherPage->html);
        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $otherPage->id,
            'summary' => 'System auto-update of internal links',
        ]);
    }

    public function test_pages_linking_to_chapter_updated_on_parent_book_url_change()
    {
        /** @var Chapter $bookChapter */
        /** @var Page $otherPage */
        /** @var Book $book */
        $bookChapter = Chapter::query()->first();
        $otherPage = Page::query()->first();
        $book = $bookChapter->book;

        $otherPage->html = '<a href="' . $bookChapter->getUrl() . '">Link</a>';
        $otherPage->save();
        $this->createReference($otherPage, $bookChapter);

        $this->asEditor()->put($book->getUrl(), [
            'name' => 'my updated book slugaroo',
        ]);

        $otherPage->refresh();
        $this->assertStringContainsString('href="http://localhost/books/my-updated-book-slugaroo/chapter/' . $bookChapter->slug . '"', $otherPage->html);
        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $otherPage->id,
            'summary' => 'System auto-update of internal links',
        ]);
    }

    public function test_markdown_links_leading_to_entity_updated_on_url_change()
    {
        /** @var Page $page */
        /** @var Book $book */
        $page = Page::query()->first();
        $book = Book::query()->first();

        $bookUrl = $book->getUrl();
        $markdown = '
        [An awesome link](' . $bookUrl . ')
        [An awesome link with query & hash](' . $bookUrl . '?test=yes#cats)
        [An awesome link with path](' . $bookUrl . '/an/extra/trail)
        [An awesome link with title](' . $bookUrl . ' "title")
        [ref]: ' . $bookUrl . '?test=yes#dogs
        [ref_without_space]:' . $bookUrl . '
        [ref_with_title]: ' . $bookUrl . ' "title"';
        $page->markdown = $markdown;
        $page->save();
        $this->createReference($page, $book);

        $this->asEditor()->put($book->getUrl(), [
            'name' => 'my updated book slugadoo',
        ]);

        $page->refresh();
        $expected = str_replace($bookUrl, 'http://localhost/books/my-updated-book-slugadoo', $markdown);
        $this->assertEquals($expected, $page->markdown);
    }

    protected function createReference(Model $from, Model $to)
    {
        (new Reference())->forceFill([
            'from_type' => $from->getMorphClass(),
            'from_id'   => $from->id,
            'to_type'   => $to->getMorphClass(),
            'to_id'     => $to->id,
        ])->save();
    }
}
