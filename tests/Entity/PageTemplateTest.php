<?php namespace Entity;

use BookStack\Entities\Page;
use Tests\TestCase;

class PageTemplateTest extends TestCase
{
    public function test_active_templates_visible_on_page_view()
    {
        $page = Page::first();

        $this->asEditor();
        $templateView = $this->get($page->getUrl());
        $templateView->assertDontSee('Page Template');

        $page->template = true;
        $page->save();

        $templateView = $this->get($page->getUrl());
        $templateView->assertSee('Page Template');
    }

    public function test_manage_templates_permission_required_to_change_page_template_status()
    {
        $page = Page::first();
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $pageUpdateData = [
            'name' => $page->name,
            'html' => $page->html,
            'template' => 'true',
        ];

        $this->put($page->getUrl(), $pageUpdateData);
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'template' => false,
        ]);

        $this->giveUserPermissions($editor, ['templates-manage']);

        $this->put($page->getUrl(), $pageUpdateData);
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'template' => true,
        ]);
    }

}