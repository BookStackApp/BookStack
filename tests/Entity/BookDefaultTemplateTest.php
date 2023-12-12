<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class BookDefaultTemplateTest extends TestCase
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

    public function test_updating_book_with_default_template()
    {
        $book = $this->entities->book();
        $templatePage = $this->entities->templatePage();

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => strval($templatePage->id)]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => $templatePage->id]);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => '']);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => null]);
    }

    public function test_default_template_cannot_be_set_if_not_a_template()
    {
        $book = $this->entities->book();
        $page = $this->entities->page();
        $this->assertFalse($page->template);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => $page->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => null]);
    }

    public function test_default_template_cannot_be_set_if_not_have_access()
    {
        $book = $this->entities->book();
        $templatePage = $this->entities->templatePage();
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => $templatePage->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => null]);
    }

    public function test_inaccessible_default_template_can_be_set_if_unchanged()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->put("/books/{$book->slug}", ['name' => $book->name, 'default_template_id' => $templatePage->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'default_template_id' => $templatePage->id]);
    }

    public function test_default_page_template_option_shows_on_book_form()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);

        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $this->withHtml($resp)->assertElementExists('input[name="default_template_id"][value="' . $templatePage->id . '"]');
    }

    public function test_default_page_template_option_only_shows_template_name_if_visible()
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

    public function test_creating_book_page_uses_default_template()
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

    public function test_creating_chapter_page_uses_default_template()
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

    public function test_creating_book_page_as_guest_uses_default_template()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My template page</p>', 'markdown' => '# My template page'])->save();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $guest = $this->users->guest();

        $this->permissions->makeAppPublic();
        $this->permissions->grantUserRolePermissions($guest, ['page-create-all', 'page-update-all']);

        $resp = $this->post($book->getUrl('/create-guest-page'), [
            'name' => 'My guest page with template'
        ]);
        $latestPage = $book->pages()
            ->where('draft', '=', false)
            ->where('template', '=', false)
            ->where('created_by', '=', $guest->id)
            ->latest()->first();

        $this->assertEquals('<p>My template page</p>', $latestPage->html);
        $this->assertEquals('# My template page', $latestPage->markdown);
    }

    public function test_creating_book_page_does_not_use_template_if_not_visible()
    {
        $templatePage = $this->entities->templatePage();
        $templatePage->forceFill(['html' => '<p>My template page</p>', 'markdown' => '# My template page'])->save();
        $book = $this->bookUsingDefaultTemplate($templatePage);
        $this->permissions->disableEntityInheritedPermissions($templatePage);

        $this->asEditor()->get($book->getUrl('/create-page'));
        $latestPage = $book->pages()
            ->where('draft', '=', true)
            ->where('template', '=', false)
            ->latest()->first();

        $this->assertEquals('', $latestPage->html);
        $this->assertEquals('', $latestPage->markdown);
    }

    public function test_template_page_delete_removes_book_template_usage()
    {
        $templatePage = $this->entities->templatePage();
        $book = $this->bookUsingDefaultTemplate($templatePage);

        $book->refresh();
        $this->assertEquals($templatePage->id, $book->default_template_id);

        $this->asEditor()->delete($templatePage->getUrl());
        $this->asAdmin()->post('/settings/recycle-bin/empty');

        $book->refresh();
        $this->assertEquals(null, $book->default_template_id);
    }

    protected function bookUsingDefaultTemplate(Page $page): Book
    {
        $book = $this->entities->book();
        $book->default_template_id = $page->id;
        $book->save();

        return $book;
    }
}
