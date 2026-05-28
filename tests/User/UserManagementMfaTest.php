<?php

namespace Tests\User;

use BookStack\Access\Mfa\MfaValue;
use BookStack\Activity\ActivityType;
use BookStack\Permissions\Permission;
use Tests\TestCase;

class UserManagementMfaTest extends TestCase
{
    public function test_configured_mfa_options_visible_on_user_edit()
    {
        $editor = $this->users->editor();

        $resp = $this->asAdmin()->get($editor->getEditUrl());
        $resp->assertSeeText('0 methods configured');

        MfaValue::factory()->create(['user_id' => $editor->id, 'method' => MfaValue::METHOD_BACKUP_CODES]);

        $resp = $this->get($editor->getEditUrl());
        $resp->assertSeeText('1 method configured');
        $resp->assertDontSeeText('0 methods configured');
    }

    public function test_reset_mfa_flow()
    {
        $editor = $this->users->editor();
        MfaValue::factory()->create(['user_id' => $editor->id, 'method' => MfaValue::METHOD_BACKUP_CODES]);
        MfaValue::factory()->create(['user_id' => $editor->id, 'method' => MfaValue::METHOD_TOTP]);

        $this->assertEquals(2, $editor->mfaValues()->count());

        $resp = $this->asAdmin()->get($editor->getEditUrl());
        $this->withHtml($resp)->assertElementContains('form[action$="/mfa"] button[type="submit"]', 'Reset');

        $resp = $this->delete($editor->getEditUrl('/mfa'));
        $resp->assertRedirect($editor->getEditUrl());
        $this->assertActivityExists(ActivityType::USER_MFA_RESET);

        $resp = $this->followRedirects($resp);
        $resp->assertSee('Multi-factor authentication methods reset');

        $this->assertEquals(0, $editor->mfaValues()->count());
    }

    public function test_users_manage_permission_required_for_mfa_reset()
    {
        $editor = $this->users->editor();
        $resp = $this->actingAs($editor)->delete($editor->getEditUrl('/mfa'));
        $this->assertPermissionError($resp);

        $this->permissions->grantUserRolePermissions($editor, [Permission::UsersManage]);

        $resp = $this->delete($editor->getEditUrl('/mfa'));
        $this->assertNotPermissionError($resp);
        $resp->assertRedirect($editor->getEditUrl());
    }
}
