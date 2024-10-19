<?php

namespace Tests\Exports;

use Tests\TestCase;

class TextExportTest extends TestCase
{
    public function test_page_text_export()
    {
        $page = $this->entities->page();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.txt"');
    }

    public function test_book_text_export()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $directPage = $book->directPages()->first();
        $chapter = $book->chapters()->first();
        $chapterPage = $chapter->pages()->first();
        $this->entities->updatePage($directPage, ['html' => '<p>My awesome page</p>']);
        $this->entities->updatePage($chapterPage, ['html' => '<p>My little nested page</p>']);
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertSee($chapterPage->name);
        $resp->assertSee($chapter->name);
        $resp->assertSee($directPage->name);
        $resp->assertSee('My awesome page');
        $resp->assertSee('My little nested page');
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.txt"');
    }

    public function test_book_text_export_format()
    {
        $entities = $this->entities->createChainBelongingToUser($this->users->viewer());
        $this->entities->updatePage($entities['page'], ['html' => '<p>My great page</p><p>Full of <strong>great</strong> stuff</p>', 'name' => 'My wonderful page!']);
        $entities['chapter']->name = 'Export chapter';
        $entities['chapter']->description = "A test chapter to be exported\nIt has loads of info within";
        $entities['book']->name = 'Export Book';
        $entities['book']->description = "This is a book with stuff to export";
        $entities['chapter']->save();
        $entities['book']->save();

        $resp = $this->asEditor()->get($entities['book']->getUrl('/export/plaintext'));

        $expected = "Export Book\nThis is a book with stuff to export\n\nExport chapter\nA test chapter to be exported\nIt has loads of info within\n\n";
        $expected .= "My wonderful page!\nMy great page Full of great stuff";
        $resp->assertSee($expected);
    }

    public function test_chapter_text_export()
    {
        $chapter = $this->entities->chapter();
        $page = $chapter->pages[0];
        $this->entities->updatePage($page, ['html' => '<p>This is content within the page!</p>']);
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertSee($page->name);
        $resp->assertSee('This is content within the page!');
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.txt"');
    }

    public function test_chapter_text_export_format()
    {
        $entities = $this->entities->createChainBelongingToUser($this->users->viewer());
        $this->entities->updatePage($entities['page'], ['html' => '<p>My great page</p><p>Full of <strong>great</strong> stuff</p>', 'name' => 'My wonderful page!']);
        $entities['chapter']->name = 'Export chapter';
        $entities['chapter']->description = "A test chapter to be exported\nIt has loads of info within";
        $entities['chapter']->save();

        $resp = $this->asEditor()->get($entities['book']->getUrl('/export/plaintext'));

        $expected = "Export chapter\nA test chapter to be exported\nIt has loads of info within\n\n";
        $expected .= "My wonderful page!\nMy great page Full of great stuff";
        $resp->assertSee($expected);
    }
}
