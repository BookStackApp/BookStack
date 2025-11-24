<?php

namespace Tests\Entity;

use Tests\TestCase;

class SlugTest extends TestCase
{
    public function test_slug_multi_byte_url_safe()
    {
        $book = $this->entities->newBook([
            'name' => 'информация',
        ]);

        $this->assertEquals('informaciia', $book->slug);

        $book = $this->entities->newBook([
            'name' => '¿Qué?',
        ]);

        $this->assertEquals('que', $book->slug);
    }

    public function test_slug_format()
    {
        $book = $this->entities->newBook([
            'name' => 'PartA / PartB / PartC',
        ]);

        $this->assertEquals('parta-partb-partc', $book->slug);
    }

    public function test_old_page_slugs_redirect_to_new_pages()
    {
        $page = $this->entities->page();

        // Need to save twice since revisions are not generated in seeder.
        $this->asAdmin()->put($page->getUrl(), [
            'name' => 'super test',
            'html' => '<p></p>',
        ]);

        $page->refresh();
        $pageUrl = $page->getUrl();

        $this->put($pageUrl, [
            'name' => 'super test page',
            'html' => '<p></p>',
        ]);

        $this->get($pageUrl)
            ->assertRedirect("/books/{$page->book->slug}/page/super-test-page");
    }

    public function test_slugs_recorded_in_history_on_page_update()
    {
        $page = $this->entities->page();
        $this->asAdmin()->put($page->getUrl(), [
            'name' => 'new slug',
            'html' => '<p></p>',
        ]);

        $oldSlug = $page->slug;
        $page->refresh();
        $this->assertNotEquals($oldSlug, $page->slug);

        $this->assertDatabaseHas('slug_history', [
            'sluggable_id' => $page->id,
            'sluggable_type' => 'page',
            'slug' => $oldSlug,
            'parent_slug' => $page->book->slug,
        ]);
    }

    public function test_slugs_recorded_in_history_on_chapter_update()
    {
        $chapter = $this->entities->chapter();
        $this->asAdmin()->put($chapter->getUrl(), [
            'name' => 'new slug',
        ]);

        $oldSlug = $chapter->slug;
        $chapter->refresh();
        $this->assertNotEquals($oldSlug, $chapter->slug);

        $this->assertDatabaseHas('slug_history', [
            'sluggable_id' => $chapter->id,
            'sluggable_type' => 'chapter',
            'slug' => $oldSlug,
            'parent_slug' => $chapter->book->slug,
        ]);
    }

    public function test_slugs_recorded_in_history_on_book_update()
    {
        $book = $this->entities->book();
        $this->asAdmin()->put($book->getUrl(), [
            'name' => 'new slug',
        ]);

        $oldSlug = $book->slug;
        $book->refresh();
        $this->assertNotEquals($oldSlug, $book->slug);

        $this->assertDatabaseHas('slug_history', [
            'sluggable_id' => $book->id,
            'sluggable_type' => 'book',
            'slug' => $oldSlug,
            'parent_slug' => null,
        ]);
    }

    public function test_slugs_recorded_in_history_on_shelf_update()
    {
        $shelf = $this->entities->shelf();
        $this->asAdmin()->put($shelf->getUrl(), [
            'name' => 'new slug',
        ]);

        $oldSlug = $shelf->slug;
        $shelf->refresh();
        $this->assertNotEquals($oldSlug, $shelf->slug);

        $this->assertDatabaseHas('slug_history', [
            'sluggable_id' => $shelf->id,
            'sluggable_type' => 'bookshelf',
            'slug' => $oldSlug,
            'parent_slug' => null,
        ]);
    }
}
