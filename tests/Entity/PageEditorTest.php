<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PageEditorTest extends TestCase
{
    /** @var Page */
    protected $page;

    protected function setUp(): void
    {
        parent::setUp();
        $this->page = Page::query()->first();
    }

    public function test_default_editor_is_wysiwyg_for_new_pages()
    {
        $this->assertEquals('wysiwyg', setting('app-editor'));
        $resp = $this->asAdmin()->get($this->page->book->getUrl('/create-page'));
        $this->followRedirects($resp)->assertElementExists('#html-editor');
    }

    public function test_markdown_setting_shows_markdown_editor_for_new_pages()
    {
        $this->setSettings(['app-editor' => 'markdown']);

        $resp = $this->asAdmin()->get($this->page->book->getUrl('/create-page'));
        $this->followRedirects($resp)
            ->assertElementNotExists('#html-editor')
            ->assertElementExists('#markdown-editor');
    }

    public function test_markdown_content_given_to_editor()
    {
        $mdContent = '# hello. This is a test';
        $this->page->markdown = $mdContent;
        $this->page->editor = 'markdown';
        $this->page->save();

        $this->asAdmin()->get($this->page->getUrl('/edit'))
            ->assertElementContains('[name="markdown"]', $mdContent);
    }

    public function test_html_content_given_to_editor_if_no_markdown()
    {
        $this->page->editor = 'markdown';
        $this->page->save();

        $this->asAdmin()->get($this->page->getUrl() . '/edit')
            ->assertElementContains('[name="markdown"]', $this->page->html);
    }

    public function test_empty_markdown_still_saves_without_error()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        /** @var Book $book */
        $book = Book::query()->first();

        $this->asEditor()->get($book->getUrl('/create-page'));
        $draft = Page::query()->where('book_id', '=', $book->id)
            ->where('draft', '=', true)->first();

        $details = [
            'name'     => 'my page',
            'markdown' => '',
        ];
        $resp = $this->post($book->getUrl("/draft/{$draft->id}"), $details);
        $resp->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'markdown' => $details['markdown'],
            'id'       => $draft->id,
            'draft'    => false,
        ]);
    }

    public function test_back_link_in_editor_has_correct_url()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->firstOrFail();
        $this->asEditor()->get($book->getUrl('/create-page'));
        /** @var Chapter $chapter */
        $chapter = $book->chapters()->firstOrFail();
        /** @var Page $draft */
        $draft = $book->pages()->where('draft', '=', true)->firstOrFail();

        // Book draft goes back to book
        $resp = $this->get($book->getUrl("/draft/{$draft->id}"));
        $resp->assertElementContains('a[href="' . $book->getUrl() . '"]', 'Back');

        // Chapter draft goes back to chapter
        $draft->chapter_id = $chapter->id;
        $draft->save();
        $resp = $this->get($book->getUrl("/draft/{$draft->id}"));
        $resp->assertElementContains('a[href="' . $chapter->getUrl() . '"]', 'Back');

        // Saved page goes back to page
        $this->post($book->getUrl("/draft/{$draft->id}"), ['name' => 'Updated', 'html' => 'Updated']);
        $draft->refresh();
        $resp = $this->get($draft->getUrl('/edit'));
        $resp->assertElementContains('a[href="' . $draft->getUrl() . '"]', 'Back');
    }

    public function test_switching_from_html_to_clean_markdown_works()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->html = '<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>';
        $page->save();

        $resp = $this->asAdmin()->get($page->getUrl('/edit?editor=markdown-clean'));
        $resp->assertStatus(200);
        $resp->assertSee("## A Header\n\nSome **bold** content.");
        $resp->assertElementExists('#markdown-editor');
    }

    public function test_switching_from_html_to_stable_markdown_works()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->html = '<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>';
        $page->save();

        $resp = $this->asAdmin()->get($page->getUrl('/edit?editor=markdown-stable'));
        $resp->assertStatus(200);
        $resp->assertSee("<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>", true);
        $resp->assertElementExists('[component="markdown-editor"]');
    }

    public function test_switching_from_markdown_to_wysiwyg_works()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->html = '';
        $page->markdown = "## A Header\n\nSome content with **bold** text!";
        $page->save();

        $resp = $this->asAdmin()->get($page->getUrl('/edit?editor=wysiwyg'));
        $resp->assertStatus(200);
        $resp->assertElementExists('[component="wysiwyg-editor"]');
        $resp->assertSee("<h2>A Header</h2>\n<p>Some content with <strong>bold</strong> text!</p>", true);
    }

    public function test_page_editor_changes_with_editor_property()
    {
        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $resp->assertElementExists('[component="wysiwyg-editor"]');

        $this->page->markdown = "## A Header\n\nSome content with **bold** text!";
        $this->page->editor = 'markdown';
        $this->page->save();

        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $resp->assertElementExists('[component="markdown-editor"]');
    }

    public function test_editor_type_switch_options_show()
    {
        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $editLink = $this->page->getUrl('/edit') . '?editor=';
        $resp->assertElementContains("a[href=\"${editLink}markdown-clean\"]", '(Clean Content)');
        $resp->assertElementContains("a[href=\"${editLink}markdown-stable\"]", '(Stable Content)');

        $resp = $this->asAdmin()->get($this->page->getUrl('/edit?editor=markdown-stable'));
        $editLink = $this->page->getUrl('/edit') . '?editor=';
        $resp->assertElementContains("a[href=\"${editLink}wysiwyg\"]", 'Switch to WYSIWYG Editor');
    }

    public function test_editor_type_switch_options_dont_show_if_without_change_editor_permissions()
    {
        $resp = $this->asEditor()->get($this->page->getUrl('/edit'));
        $editLink = $this->page->getUrl('/edit') . '?editor=';
        $resp->assertElementNotExists("a[href*=\"${editLink}\"]");
    }

}
