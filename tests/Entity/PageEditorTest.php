<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PageEditorTest extends TestCase
{
    protected Page $page;

    protected function setUp(): void
    {
        parent::setUp();
        $this->page = $this->entities->page();
    }

    public function test_default_editor_is_wysiwyg_for_new_pages()
    {
        $this->assertEquals('wysiwyg', setting('app-editor'));
        $resp = $this->asAdmin()->get($this->page->book->getUrl('/create-page'));
        $this->withHtml($this->followRedirects($resp))->assertElementExists('#html-editor');
    }

    public function test_markdown_setting_shows_markdown_editor_for_new_pages()
    {
        $this->setSettings(['app-editor' => 'markdown']);

        $resp = $this->asAdmin()->get($this->page->book->getUrl('/create-page'));
        $this->withHtml($this->followRedirects($resp))
            ->assertElementNotExists('#html-editor')
            ->assertElementExists('#markdown-editor');
    }

    public function test_markdown_content_given_to_editor()
    {
        $mdContent = '# hello. This is a test';
        $this->page->markdown = $mdContent;
        $this->page->editor = 'markdown';
        $this->page->save();

        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $this->withHtml($resp)->assertElementContains('[name="markdown"]', $mdContent);
    }

    public function test_html_content_given_to_editor_if_no_markdown()
    {
        $this->page->editor = 'markdown';
        $this->page->save();

        $resp = $this->asAdmin()->get($this->page->getUrl() . '/edit');
        $this->withHtml($resp)->assertElementContains('[name="markdown"]', $this->page->html);
    }

    public function test_empty_markdown_still_saves_without_error()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        $book = $this->entities->book();

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
        $book = $this->entities->bookHasChaptersAndPages();
        $this->asEditor()->get($book->getUrl('/create-page'));
        /** @var Chapter $chapter */
        $chapter = $book->chapters()->firstOrFail();
        /** @var Page $draft */
        $draft = $book->pages()->where('draft', '=', true)->firstOrFail();

        // Book draft goes back to book
        $resp = $this->get($book->getUrl("/draft/{$draft->id}"));
        $this->withHtml($resp)->assertElementContains('a[href="' . $book->getUrl() . '"]', 'Back');

        // Chapter draft goes back to chapter
        $draft->chapter_id = $chapter->id;
        $draft->save();
        $resp = $this->get($book->getUrl("/draft/{$draft->id}"));
        $this->withHtml($resp)->assertElementContains('a[href="' . $chapter->getUrl() . '"]', 'Back');

        // Saved page goes back to page
        $this->post($book->getUrl("/draft/{$draft->id}"), ['name' => 'Updated', 'html' => 'Updated']);
        $draft->refresh();
        $resp = $this->get($draft->getUrl('/edit'));
        $this->withHtml($resp)->assertElementContains('a[href="' . $draft->getUrl() . '"]', 'Back');
    }

    public function test_switching_from_html_to_clean_markdown_works()
    {
        $page = $this->entities->page();
        $page->html = '<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>';
        $page->save();

        $resp = $this->asAdmin()->get($page->getUrl('/edit?editor=markdown-clean'));
        $resp->assertStatus(200);
        $resp->assertSee("## A Header\n\nSome **bold** content.");
        $this->withHtml($resp)->assertElementExists('#markdown-editor');
    }

    public function test_switching_from_html_to_stable_markdown_works()
    {
        $page = $this->entities->page();
        $page->html = '<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>';
        $page->save();

        $resp = $this->asAdmin()->get($page->getUrl('/edit?editor=markdown-stable'));
        $resp->assertStatus(200);
        $resp->assertSee('<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>', true);
        $this->withHtml($resp)->assertElementExists('[component="markdown-editor"]');
    }

    public function test_switching_from_markdown_to_wysiwyg_works()
    {
        $page = $this->entities->page();
        $page->html = '';
        $page->markdown = "## A Header\n\nSome content with **bold** text!";
        $page->save();

        $resp = $this->asAdmin()->get($page->getUrl('/edit?editor=wysiwyg'));
        $resp->assertStatus(200);
        $this->withHtml($resp)->assertElementExists('[component="wysiwyg-editor"]');
        $resp->assertSee("<h2>A Header</h2>\n<p>Some content with <strong>bold</strong> text!</p>", true);
    }

    public function test_page_editor_changes_with_editor_property()
    {
        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $this->withHtml($resp)->assertElementExists('[component="wysiwyg-editor"]');

        $this->page->markdown = "## A Header\n\nSome content with **bold** text!";
        $this->page->editor = 'markdown';
        $this->page->save();

        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $this->withHtml($resp)->assertElementExists('[component="markdown-editor"]');
    }

    public function test_editor_type_switch_options_show()
    {
        $resp = $this->asAdmin()->get($this->page->getUrl('/edit'));
        $editLink = $this->page->getUrl('/edit') . '?editor=';
        $this->withHtml($resp)->assertElementContains("a[href=\"${editLink}markdown-clean\"]", '(Clean Content)');
        $this->withHtml($resp)->assertElementContains("a[href=\"${editLink}markdown-stable\"]", '(Stable Content)');

        $resp = $this->asAdmin()->get($this->page->getUrl('/edit?editor=markdown-stable'));
        $editLink = $this->page->getUrl('/edit') . '?editor=';
        $this->withHtml($resp)->assertElementContains("a[href=\"${editLink}wysiwyg\"]", 'Switch to WYSIWYG Editor');
    }

    public function test_editor_type_switch_options_dont_show_if_without_change_editor_permissions()
    {
        $resp = $this->asEditor()->get($this->page->getUrl('/edit'));
        $editLink = $this->page->getUrl('/edit') . '?editor=';
        $this->withHtml($resp)->assertElementNotExists("a[href*=\"${editLink}\"]");
    }

    public function test_page_editor_type_switch_does_not_work_without_change_editor_permissions()
    {
        $page = $this->entities->page();
        $page->html = '<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>';
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/edit?editor=markdown-stable'));
        $resp->assertStatus(200);
        $this->withHtml($resp)->assertElementExists('[component="wysiwyg-editor"]');
        $this->withHtml($resp)->assertElementNotExists('[component="markdown-editor"]');
    }

    public function test_page_save_does_not_change_active_editor_without_change_editor_permissions()
    {
        $page = $this->entities->page();
        $page->html = '<h2>A Header</h2><p>Some <strong>bold</strong> content.</p>';
        $page->editor = 'wysiwyg';
        $page->save();

        $this->asEditor()->put($page->getUrl(), ['name' => $page->name, 'markdown' => '## Updated content abc']);
        $this->assertEquals('wysiwyg', $page->refresh()->editor);
    }
}
