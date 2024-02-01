<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class DefaultTemplateTest extends TestCase
{
    public function test_creating_book_with_default_template()
    {
        $templatePage = $this->entities->templatePage();
        $details = [
            'name' => 'My book with default template',
            'default_template_id' => $templatePage->id,
        ];

        $this->asEditor()->post('/books', $details);
        $this->assertDatabaseHas('books', $details);
    }

    public function test_creating_chapter_with_default_template()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->entities->book();
        $details = [
            'name' => 'My chapter with default template',
            'default_template_id' => $templatePage->id,
        ];

        $this->asEditor()->post($book->getUrl('/create-chapter'), $details);
        $this->assertDatabaseHas('chapters', $details);
    }

    public function test_updating_book_with_default_template()
    {
        $book = $this->entities->book();
        $templatePage = $this->entities->templatePage();

        $this->asEditor()->put($book->getUrl(), ['name' => $book->name, 'default_template_id' => strval($templatePage->id)]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => $templatePage->id]);

        $this->asEditor()->put($book->getUrl(), ['name' => $book->name, 'default_template_id' => '']);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => null]);
    }

    public function test_updating_chapter_with_default_template()
    {
        $chapter = $this->entities->chapter();
        $templatePage = $this->entities->templatePage();

        $this->asEditor()->put($chapter->getUrl(), ['name' => $chapter->name, 'default_template_id' => strval($templatePage->id)]);
        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'default_template_id' => $templatePage->id]);

        $this->asEditor()->put($chapter->getUrl(), ['name' => $chapter->name, 'default_template_id' => '']);
        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'default_template_id' => null]);
    }

    public function test_default_book_template_cannot_be_set_if_not_a_template()
    {
        $book = $this->entities->book();
        $page = $this->entities->page();
        $this->assertFalse($page->template);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => $page->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => null]);
    }

    public function test_default_chapter_template_cannot_be_set_if_not_a_template()
    {
        $chapter = $this->entities->chapter();
        $page = $this->entities->page();
        $this->assertFalse($page->template);

        $this->asEditor()->put("/chapters/{$chapter->slug}", ['name' => $chapter->name, 'default_template_id' => $page->id]);
        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'default_template_id' => null]);
    }


    public function test_default_book_template_cannot_be_set_if_not_have_access()
    {
        $book = $this->entities->book();
        $templatePage = $this->entities->templatePage();
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => $templatePage->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => null]);
    }

    public function test_default_chapter_template_cannot_be_set_if_not_have_access()
    {
        $chapter = $this->entities->chapter();
        $templatePage = $this->entities->templatePage();
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->put("/chapters/{$chapter->slug}", ['name' => $chapter->name, 'default_template_id' => $templatePage->id]);
        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'default_template_id' => null]);
    }

    public function test_inaccessible_book_default_template_can_be_set_if_unchanged()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => $templatePage->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => $templatePage->id]);
    }

    public function test_inaccessible_chapter_default_template_can_be_set_if_unchanged()
    {
        $templatePage = $this->entities->templatePage();
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->put("/chapters/{$chapter->slug}", ['name' => $chapter->name, 'default_template_id' => $templatePage->id]);
        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'default_template_id' => $templatePage->id]);
    }

    public function test_default_page_template_option_shows_on_book_form()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);

        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $this->withHtml($resp)->assertElementExists('input[name="default_template_id"][value="' . $templatePage->id . '"]');
    }

    public function test_default_page_template_option_shows_on_chapter_form()
    {
        $templatePage = $this->entities->templatePage();
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);

        $resp = $this->asEditor()->get($chapter->getUrl('/edit'));
        $this->withHtml($resp)->assertElementExists('input[name="default_template_id"][value="' . $templatePage->id . '"]');
    }

    public function test_book_default_page_template_option_only_shows_template_name_if_visible()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);

        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $this->withHtml($resp)->assertElementContains('#template-control a.text-page', "#{$templatePage->id}, {$templatePage->name}");

        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $this->withHtml($resp)->assertElementNotContains('#template-control a.text-page', "#{$templatePage->id}, {$templatePage->name}");
        $this->withHtml($resp)->assertElementContains('#template-control a.text-page', "#{$templatePage->id}");
    }

    public function test_chapter_default_page_template_option_only_shows_template_name_if_visible()
    {
        $templatePage = $this->entities->templatePage();
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);

        $resp = $this->asEditor()->get($chapter->getUrl('/edit'));
        $this->withHtml($resp)->assertElementContains('#template-control a.text-page', "#{$templatePage->id}, {$templatePage->name}");

        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $resp = $this->asEditor()->get($chapter->getUrl('/edit'));
        $this->withHtml($resp)->assertElementNotContains('#template-control a.text-page', "#{$templatePage->id}, {$templatePage->name}");
        $this->withHtml($resp)->assertElementContains('#template-control a.text-page', "#{$templatePage->id}");
    }

    public function test_creating_book_page_uses_book_default_template()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My template page</p>', 'markdown' => '# My template page'])->save();
        $book = $this->bookUsingDefaultTemplate($templatePage);

        $this->asEditor()->get($book->getUrl('/create-page'));
        $latestPage = $book->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('<p>My template page</p>', $latestPage->html);
        $this->assertEquals('# My template page', $latestPage->markdown);
    }

    public function test_creating_chapter_page_uses_chapter_default_template()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My chapter template page</p>', 'markdown' => '# My chapter template page'])->save();
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);

        $this->asEditor()->get($chapter->getUrl('/create-page'));
        $latestPage = $chapter->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('<p>My chapter template page</p>', $latestPage->html);
        $this->assertEquals('# My chapter template page', $latestPage->markdown);
    }

    public function test_creating_chapter_page_uses_book_default_template_if_no_chapter_template_set()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My template page in chapter</p>', 'markdown' => '# My template page in chapter'])->save();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $chapter = $book->chapters()->first();

        $this->asEditor()->get($chapter->getUrl('/create-page'));
        $latestPage = $chapter->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('<p>My template page in chapter</p>', $latestPage->html);
        $this->assertEquals('# My template page in chapter', $latestPage->markdown);
    }

    public function test_creating_chapter_page_uses_chapter_template_instead_of_book_template()
    {
        $bookTemplatePage = $this->entities->templatePage();
        $bookTemplatePage->forceFill(['html' => '<p>My book template</p>', 'markdown' => '# My book template'])->save();
        $book = $this->bookUsingDefaultTemplate($bookTemplatePage);

        $chapterTemplatePage = $this->entities->templatePage();
        $chapterTemplatePage->forceFill(['html' => '<p>My chapter template</p>', 'markdown' => '# My chapter template'])->save();
        $chapter = $book->chapters()->first();
        $chapter->default_template_id = $chapterTemplatePage->id;
        $chapter->save();

        $this->asEditor()->get($chapter->getUrl('/create-page'));
        $latestPage = $chapter->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('<p>My chapter template</p>', $latestPage->html);
        $this->assertEquals('# My chapter template', $latestPage->markdown);
    }

    public function test_creating_page_as_guest_uses_default_template()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My template page</p>', 'markdown' => '# My template page'])->save();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);
        $guest = $this->users->guest();

        $this->permissions->makeAppPublic();
        $this->permissions->grantUserRolePermissions($guest, ['page-create-all', 'page-update-all']);

        $this->post($book->getUrl('/create-guest-page'), [
            'name' => 'My guest page with template'
        ]);
        $latestBookPage = $book->pages()
            ->where('draft', '=', false)
            ->where('template', '=', false)
            ->where('created_by', '=', $guest->id)
            ->latest()->first();

        $this->assertEquals('<p>My template page</p>', $latestBookPage->html);
        $this->assertEquals('# My template page', $latestBookPage->markdown);

        $this->post($chapter->getUrl('/create-guest-page'), [
            'name' => 'My guest page with template'
        ]);
        $latestChapterPage = $chapter->pages()
            ->where('draft', '=', false)
            ->where('template', '=', false)
            ->where('created_by', '=', $guest->id)
            ->latest()->first();

        $this->assertEquals('<p>My template page</p>', $latestChapterPage->html);
        $this->assertEquals('# My template page', $latestChapterPage->markdown);
    }

    public function test_templates_not_used_if_not_visible()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My template page</p>', 'markdown' => '# My template page'])->save();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);

        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->get($book->getUrl('/create-page'));
        $latestBookPage = $book->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('', $latestBookPage->html);
        $this->assertEquals('', $latestBookPage->markdown);

        $this->asEditor()->get($chapter->getUrl('/create-page'));
        $latestChapterPage = $chapter->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('', $latestChapterPage->html);
        $this->assertEquals('', $latestChapterPage->markdown);
    }

    public function test_template_page_delete_removes_template_usage()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $chapter = $this->chapterUsingDefaultTemplate($templatePage);

        $book->refresh();
        $this->assertEquals($templatePage->id, $book->default_template_id);
        $this->assertEquals($templatePage->id, $chapter->default_template_id);

        $this->asEditor()->delete($templatePage->getUrl());
        $this->asAdmin()->post('/settings/recycle-bin/empty');

        $book->refresh();
        $chapter->refresh();
        $this->assertEquals(null, $book->default_template_id);
        $this->assertEquals(null, $chapter->default_template_id);
    }

    protected function bookUsingDefaultTemplate(Page $page): Book
    {
        $book = $this->entities->book();
        $book->default_template_id = $page->id;
        $book->save();

        return $book;
    }

    protected function chapterUsingDefaultTemplate(Page $page): Chapter
    {
        $chapter = $this->entities->chapter();
        $chapter->default_template_id = $page->id;
        $chapter->save();

        return $chapter;
    }
}
