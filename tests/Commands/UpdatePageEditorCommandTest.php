<?php

namespace Tests\Commands;

use BookStack\Entities\Models\EntityPageData;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageEditorType;
use Tests\TestCase;

class UpdatePageEditorCommandTest extends TestCase
{
    public function test_it_reports_existing_usage()
    {
        $mdPage = $this->entities->page();
        $legacyPage = $this->entities->page();
        $mdPage->editor = PageEditorType::Markdown->value;
        $mdPage->save();
        $legacyPage->editor = PageEditorType::WysiwygTinymce->value;
        $legacyPage->save();

        $expectedNewCount = Page::query()->count() - 2;

        $this->artisan('bookstack:update-page-editor')
            ->expectsOutputToContain('Editor Use:')
            ->expectsOutputToContain('- WYSIWYG (legacy): 1 pages')
            ->expectsOutputToContain("- WYSIWYG (new):    {$expectedNewCount} pages")
            ->expectsOutputToContain('- Markdown:         1 pages')
            ->expectsQuestion('What editor would you like to set as the default?', 'Exit with no change');
    }

    public function test_it_confirms_existing_default()
    {
        $this->artisan('bookstack:update-page-editor')
            ->expectsOutputToContain('Current Default: WYSIWYG (new)')
            ->expectsQuestion('What editor would you like to set as the default?', 'Exit with no change');

        setting()->put('app-editor', PageEditorType::Markdown->value);

        $this->artisan('bookstack:update-page-editor')
            ->expectsOutputToContain('Current Default: Markdown')
            ->expectsQuestion('What editor would you like to set as the default?', 'Exit with no change');
    }

    public function test_it_updates_the_default_editor_setting()
    {
        $this->assertEquals('wysiwyg2024', setting('app-editor'));

        $this->artisan('bookstack:update-page-editor')
            ->expectsOutputToContain('Current Default: WYSIWYG (new)')
            ->expectsQuestion('What editor would you like to set as the default?', 'Markdown');

        $this->assertEquals('markdown', setting('app-editor'));
    }

    public function test_selecting_a_wysiwyg_option_allows_migration()
    {
        $totalPageCount = EntityPageData::query()->count();
        $this->assertEquals(0, EntityPageData::query()->where('editor', '=', 'wysiwyg')->count());

        $this->artisan('bookstack:update-page-editor')
            ->expectsQuestion('What editor would you like to set as the default?', 'WYSIWYG (legacy)')
            ->expectsOutputToContain("There are {$totalPageCount} pages still using the WYSIWYG (new) editor")
            ->expectsOutputToContain('These pages could be updated to use the WYSIWYG (legacy) editor instead.')
            ->expectsOutputToContain('WARNING: No page content will be altered right now, but changing the editor for a page can lead to formatting/content loss when a page is opened for editing.')
            ->expectsConfirmation('Update these pages to use your new editor choice?', 'yes')
            ->expectsOutputToContain("{$totalPageCount} pages have been updated to use the WYSIWYG (legacy) editor!")
            ->assertExitCode(0);

        $this->assertEquals($totalPageCount, EntityPageData::query()->where('editor', '=', 'wysiwyg')->count());
    }

    public function test_selecting_markdown_does_not_prompt_for_migration()
    {
        $this->artisan('bookstack:update-page-editor')
            ->expectsQuestion('What editor would you like to set as the default?', 'Markdown')
            ->doesntExpectOutputToContain('These pages could be updated')
            ->assertExitCode(0);
    }

    public function test_no_migration_prompt_if_nothing_to_migrate()
    {
        $this->artisan('bookstack:update-page-editor')
            ->expectsQuestion('What editor would you like to set as the default?', 'WYSIWYG (new)')
            ->doesntExpectOutputToContain('These pages could be updated')
            ->assertExitCode(0);
    }
}
