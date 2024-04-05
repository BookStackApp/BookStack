<?php

namespace Tests\References;

use BookStack\App\Model;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\TrashCan;
use BookStack\References\Reference;
use Tests\TestCase;

class ReferencesTest extends TestCase
{
    public function test_references_created_on_page_update()
    {
        $pageA = $this->entities->page();
        $pageB = $this->entities->page();

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

    public function test_references_created_on_book_chapter_bookshelf_update()
    {
        $entities = [$this->entities->book(), $this->entities->chapter(), $this->entities->shelf()];
        $shelf = $this->entities->shelf();

        foreach ($entities as $entity) {
            $entity->refresh();
            $this->assertDatabaseMissing('references', ['from_id' => $entity->id, 'from_type' => $entity->getMorphClass()]);

            $this->asEditor()->put($entity->getUrl(), [
                'name' => 'Reference test',
                'description_html' => '<a href="' . $shelf->getUrl() . '">Testing</a>',
            ]);

            $this->assertDatabaseHas('references', [
                'from_id'   => $entity->id,
                'from_type' => $entity->getMorphClass(),
                'to_id'     => $shelf->id,
                'to_type'   => $shelf->getMorphClass(),
            ]);
        }
    }

    public function test_references_deleted_on_page_delete()
    {
        $pageA = $this->entities->page();
        $pageB = $this->entities->page();

        $this->createReference($pageA, $pageB);
        $this->createReference($pageB, $pageA);

        $this->assertDatabaseHas('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);
        $this->assertDatabaseHas('references', ['to_id' => $pageA->id, 'to_type' => $pageA->getMorphClass()]);

        app(PageRepo::class)->destroy($pageA);
        app(TrashCan::class)->empty();

        $this->assertDatabaseMissing('references', ['from_id' => $pageA->id, 'from_type' => $pageA->getMorphClass()]);
        $this->assertDatabaseMissing('references', ['to_id' => $pageA->id, 'to_type' => $pageA->getMorphClass()]);
    }

    public function test_references_from_deleted_on_book_chapter_shelf_delete()
    {
        $entities = [$this->entities->chapter(), $this->entities->book(), $this->entities->shelf()];
        $shelf = $this->entities->shelf();

        foreach ($entities as $entity) {
            $this->createReference($entity, $shelf);
            $this->assertDatabaseHas('references', ['from_id' => $entity->id, 'from_type' => $entity->getMorphClass()]);

            $this->asEditor()->delete($entity->getUrl());
            app(TrashCan::class)->empty();

            $this->assertDatabaseMissing('references', [
                'from_id'   => $entity->id,
                'from_type' => $entity->getMorphClass()
            ]);
        }
    }

    public function test_references_to_count_visible_on_entity_show_view()
    {
        $entities = $this->entities->all();
        $otherPage = $this->entities->page();

        $this->asEditor();
        foreach ($entities as $entity) {
            $this->createReference($entities['page'], $entity);
        }

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $resp->assertSee('Referenced by 1 item');
            $resp->assertDontSee('Referenced by 1 items');
        }

        $this->createReference($otherPage, $entities['page']);
        $resp = $this->get($entities['page']->getUrl());
        $resp->assertSee('Referenced by 2 items');
    }

    public function test_references_to_visible_on_references_page()
    {
        $entities = $this->entities->all();
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
        $page = $this->entities->page();
        $pageB = $this->entities->page();
        $this->createReference($pageB, $page);

        $this->permissions->setEntityPermissions($pageB);

        $this->asEditor()->get($page->getUrl('/references'))->assertDontSee($pageB->name);
        $this->asAdmin()->get($page->getUrl('/references'))->assertSee($pageB->name);
    }

    public function test_reference_page_shows_empty_state_with_no_references()
    {
        $page = $this->entities->page();

        $this->asEditor()
            ->get($page->getUrl('/references'))
            ->assertSee('There are no tracked references');
    }

    public function test_pages_leading_to_entity_updated_on_url_change()
    {
        $pageA = $this->entities->page();
        $pageB = $this->entities->page();
        $book = $this->entities->book();

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
        $bookPage = $this->entities->page();
        $otherPage = $this->entities->page();
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
        $bookChapter = $this->entities->chapter();
        $otherPage = $this->entities->page();
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
        $page = $this->entities->page();
        $book = $this->entities->book();

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

    public function test_description_links_from_book_chapter_shelf_updated_on_url_change()
    {
        $entities = [$this->entities->chapter(), $this->entities->book(), $this->entities->shelf()];
        $shelf = $this->entities->shelf();
        $this->asEditor();

        foreach ($entities as $entity) {
            $this->put($entity->getUrl(), [
                'name' => 'Reference test',
                'description_html' => '<a href="' . $shelf->getUrl() . '">Testing</a>',
            ]);
        }

        $oldUrl = $shelf->getUrl();
        $this->put($shelf->getUrl(), ['name' => 'My updated shelf link']);
        $shelf->refresh();
        $this->assertNotEquals($oldUrl, $shelf->getUrl());

        foreach ($entities as $entity) {
            $oldHtml = $entity->description_html;
            $entity->refresh();
            $this->assertNotEquals($oldHtml, $entity->description_html);
            $this->assertStringContainsString($shelf->getUrl(), $entity->description_html);
        }
    }

    public function test_reference_from_deleted_item_does_not_count_or_show_in_references_page()
    {
        $page = $this->entities->page();
        $referencingPageA = $this->entities->page();
        $referencingPageB = $this->entities->page();

        $this->asEditor();
        $this->createReference($referencingPageA, $page);
        $this->createReference($referencingPageB, $page);

        $resp = $this->get($page->getUrl());
        $resp->assertSee('Referenced by 2 items');

        $this->delete($referencingPageA->getUrl());

        $resp = $this->get($page->getUrl());
        $resp->assertSee('Referenced by 1 item');

        $resp = $this->get($page->getUrl('/references'));
        $resp->assertOk();
        $resp->assertSee($referencingPageB->getUrl());
        $resp->assertDontSee($referencingPageA->getUrl());
    }

    protected function createReference(Model $from, Model $to): void
    {
        (new Reference())->forceFill([
            'from_type' => $from->getMorphClass(),
            'from_id'   => $from->id,
            'to_type'   => $to->getMorphClass(),
            'to_id'     => $to->id,
        ])->save();
    }
}
