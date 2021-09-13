<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PageEditorTest extends TestCase
{
    /** @var Page  */
    protected $page;

    public function setUp(): void
    {
        parent::setUp();
        $this->page = Page::query()->first();
    }

    public function test_default_editor_is_wysiwyg()
    {
        $this->assertEquals('wysiwyg', setting('app-editor'));
        $this->asAdmin()->get($this->page->getUrl() . '/edit')
            ->assertElementExists('#html-editor');
    }

    public function test_markdown_setting_shows_markdown_editor()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        $this->asAdmin()->get($this->page->getUrl() . '/edit')
            ->assertElementNotExists('#html-editor')
            ->assertElementExists('#markdown-editor');
    }

    public function test_markdown_content_given_to_editor()
    {
        $this->setSettings(['app-editor' => 'markdown']);

        $mdContent = '# hello. This is a test';
        $this->page->markdown = $mdContent;
        $this->page->save();

        $this->asAdmin()->get($this->page->getUrl() . '/edit')
            ->assertElementContains('[name="markdown"]', $mdContent);
    }

    public function test_html_content_given_to_editor_if_no_markdown()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        $this->asAdmin()->get($this->page->getUrl() . '/edit')
            ->assertElementContains('[name="markdown"]', $this->page->html);
    }
}