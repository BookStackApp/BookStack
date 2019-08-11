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

    public function test_templates_content_should_be_fetchable_only_if_page_marked_as_template()
    {
        $content = '<div>my_custom_template_content</div>';
        $page = Page::first();
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $templateFetch = $this->get('/templates/' . $page->id);
        $templateFetch->assertStatus(404);

        $page->html = $content;
        $page->template = true;
        $page->save();

        $templateFetch = $this->get('/templates/' . $page->id);
        $templateFetch->assertStatus(200);
        $templateFetch->assertJson([
            'html' => $content,
            'markdown' => '',
        ]);
    }

    public function test_template_endpoint_returns_paginated_list_of_templates()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $toBeTemplates = Page::query()->take(12)->get();
        $page = $toBeTemplates->first();

        $emptyTemplatesFetch = $this->get('/templates');
        $emptyTemplatesFetch->assertDontSee($page->name);

        Page::query()->whereIn('id', $toBeTemplates->pluck('id')->toArray())->update(['template' => true]);

        $templatesFetch = $this->get('/templates');
        $templatesFetch->assertSee($page->name);
        $templatesFetch->assertSee('pagination');
    }

}