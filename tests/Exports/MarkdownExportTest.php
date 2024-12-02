<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Book;
use Tests\TestCase;

class MarkdownExportTest extends TestCase
{
    public function test_page_markdown_export()
    {
        $page = $this->entities->page();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.md"');
    }

    public function test_page_markdown_export_uses_existing_markdown_if_apparent()
    {
        $page = $this->entities->page()->forceFill([
            'markdown' => '# A header',
            'html'     => '<h1>Dogcat</h1>',
        ]);
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertSee('A header');
        $resp->assertDontSee('Dogcat');
    }

    public function test_page_markdown_export_converts_html_where_no_markdown()
    {
        $page = $this->entities->page()->forceFill([
            'markdown' => '',
            'html'     => '<h1>Dogcat</h1><p>Some <strong>bold</strong> text</p>',
        ]);
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertSee("# Dogcat\n\nSome **bold** text");
    }

    public function test_chapter_markdown_export()
    {
        $chapter = $this->entities->chapter();
        $chapter->description_html = '<p>My <strong>chapter</strong> description</p>';
        $chapter->save();
        $page = $chapter->pages()->first();

        $resp = $this->asEditor()->get($chapter->getUrl('/export/markdown'));

        $resp->assertSee('# ' . $chapter->name);
        $resp->assertSee('# ' . $page->name);
        $resp->assertSee('My **chapter** description');
    }

    public function test_book_markdown_export()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->first();
        $book->description_html = '<p>My <strong>book</strong> description</p>';
        $book->save();

        $chapter = $book->chapters()->first();
        $chapter->description_html = '<p>My <strong>chapter</strong> description</p>';
        $chapter->save();

        $page = $chapter->pages()->first();
        $resp = $this->asEditor()->get($book->getUrl('/export/markdown'));

        $resp->assertSee('# ' . $book->name);
        $resp->assertSee('# ' . $chapter->name);
        $resp->assertSee('# ' . $page->name);
        $resp->assertSee('My **book** description');
        $resp->assertSee('My **chapter** description');
    }

    public function test_book_markdown_export_concats_immediate_pages_with_newlines()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('pages')->first();

        $this->asEditor()->get($book->getUrl('/create-page'));
        $this->get($book->getUrl('/create-page'));

        [$pageA, $pageB] = $book->pages()->where('chapter_id', '=', 0)->get();
        $pageA->html = '<p>hello tester</p>';
        $pageA->save();
        $pageB->name = 'The second page in this test';
        $pageB->save();

        $resp = $this->get($book->getUrl('/export/markdown'));
        $resp->assertDontSee('hello tester# The second page in this test');
        $resp->assertSee("hello tester\n\n# The second page in this test");
    }
}
