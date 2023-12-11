<?php

namespace Tests\Settings;

use BookStack\Activity\ActivityType;
use BookStack\References\ReferenceStore;
use Tests\TestCase;

class RegenerateReferencesTest extends TestCase
{
    public function test_option_visible_on_maintenance_page()
    {
        $pageView = $this->asAdmin()->get('/settings/maintenance');
        $formCssSelector = 'form[action$="/settings/maintenance/regenerate-references"]';
        $html = $this->withHtml($pageView);
        $html->assertElementExists('#regenerate-references');
        $html->assertElementExists($formCssSelector);
        $html->assertElementContains($formCssSelector . ' button', 'Regenerate References');
    }

    public function test_action_runs_reference_regen()
    {
        $this->mock(ReferenceStore::class)
            ->shouldReceive('updateForAllPages')
            ->once();

        $resp = $this->asAdmin()->post('/settings/maintenance/regenerate-references');
        $resp->assertRedirect('/settings/maintenance#regenerate-references');
        $this->assertSessionHas('success', 'Reference index has been regenerated!');
        $this->assertActivityExists(ActivityType::MAINTENANCE_ACTION_RUN, null, 'regenerate-references');
    }

    public function test_settings_manage_permission_required()
    {
        $editor = $this->users->editor();
        $resp = $this->actingAs($editor)->post('/settings/maintenance/regenerate-references');
        $this->assertPermissionError($resp);

        $this->permissions->grantUserRolePermissions($editor, ['settings-manage']);

        $resp = $this->actingAs($editor)->post('/settings/maintenance/regenerate-references');
        $this->assertNotPermissionError($resp);
    }

    public function test_action_failed_shown_as_error_notification()
    {
        $this->mock(ReferenceStore::class)
            ->shouldReceive('updateForAllPages')
            ->andThrow(\Exception::class, 'A badger stopped the task');

        $resp = $this->asAdmin()->post('/settings/maintenance/regenerate-references');
        $resp->assertRedirect('/settings/maintenance#regenerate-references');
        $this->assertSessionError('A badger stopped the task');
    }
}
