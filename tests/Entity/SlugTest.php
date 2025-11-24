<?php

namespace Tests\Entity;

use BookStack\Entities\Models\SlugHistory;
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
        $pageUrl = $page->getUrl();

        $this->asAdmin()->put($pageUrl, [
            'name' => 'super test page',
            'html' => '<p></p>',
        ]);

        $this->get($pageUrl)
            ->assertRedirect("/books/{$page->book->slug}/page/super-test-page");
    }

    public function test_old_shelf_slugs_redirect_to_new_shelf()
    {
        $shelf = $this->entities->shelf();
        $shelfUrl = $shelf->getUrl();

        $this->asAdmin()->put($shelf->getUrl(), [
            'name' => 'super test shelf',
        ]);

        $this->get($shelfUrl)
            ->assertRedirect("/shelves/super-test-shelf");
    }

    public function test_old_book_slugs_redirect_to_new_book()
    {
        $book = $this->entities->book();
        $bookUrl = $book->getUrl();

        $this->asAdmin()->put($book->getUrl(), [
            'name' => 'super test book',
        ]);

        $this->get($bookUrl)
            ->assertRedirect("/books/super-test-book");
    }

    public function test_old_chapter_slugs_redirect_to_new_chapter()
    {
        $chapter = $this->entities->chapter();
        $chapterUrl = $chapter->getUrl();

        $this->asAdmin()->put($chapter->getUrl(), [
            'name' => 'super test chapter',
        ]);

        $this->get($chapterUrl)
            ->assertRedirect("/books/{$chapter->book->slug}/chapter/super-test-chapter");
    }

    public function test_old_book_slugs_in_page_urls_redirect_to_current_page_url()
    {
        $page = $this->entities->page();
        $book = $page->book;
        $pageUrl = $page->getUrl();

        $this->asAdmin()->put($book->getUrl(), [
            'name' => 'super test book',
        ]);

        $this->get($pageUrl)
            ->assertRedirect("/books/super-test-book/page/{$page->slug}");
    }

    public function test_old_book_slugs_in_chapter_urls_redirect_to_current_chapter_url()
    {
        $chapter = $this->entities->chapter();
        $book = $chapter->book;
        $chapterUrl = $chapter->getUrl();

        $this->asAdmin()->put($book->getUrl(), [
            'name' => 'super test book',
        ]);

        $this->get($chapterUrl)
            ->assertRedirect("/books/super-test-book/chapter/{$chapter->slug}");
    }

    public function test_slug_lookup_controlled_by_permissions()
    {
        $editor = $this->users->editor();
        $pageA = $this->entities->page();
        $pageB = $this->entities->page();

        SlugHistory::factory()->create(['sluggable_id' => $pageA->id, 'sluggable_type' => 'page', 'slug' => 'monkey', 'parent_slug' => 'animals', 'created_at' => now()]);
        SlugHistory::factory()->create(['sluggable_id' => $pageB->id, 'sluggable_type' => 'page', 'slug' => 'monkey', 'parent_slug' => 'animals', 'created_at' => now()->subDay()]);

        // Defaults to latest where visible
        $this->actingAs($editor)->get("/books/animals/page/monkey")->assertRedirect($pageA->getUrl());

        $this->permissions->disableEntityInheritedPermissions($pageA);

        // Falls back to other entry where the latest is not visible
        $this->actingAs($editor)->get("/books/animals/page/monkey")->assertRedirect($pageB->getUrl());

        // Original still accessible where permissions allow
        $this->asAdmin()->get("/books/animals/page/monkey")->assertRedirect($pageA->getUrl());
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
