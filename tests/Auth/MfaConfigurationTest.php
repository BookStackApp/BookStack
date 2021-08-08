<?php

namespace Tests\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Auth\User;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class MfaConfigurationTest extends TestCase
{

    public function test_totp_setup()
    {
        $editor = $this->getEditor();
        $this->assertDatabaseMissing('mfa_values', ['user_id' => $editor->id]);

        // Setup page state
        $resp = $this->actingAs($editor)->get('/mfa/setup');
        $resp->assertElementContains('a[href$="/mfa/totp/generate"]', 'Setup');

        // Generate page access
        $resp = $this->get('/mfa/totp/generate');
        $resp->assertSee('Mobile App Setup');
        $resp->assertSee('Verify Setup');
        $resp->assertElementExists('form[action$="/mfa/totp/confirm"] button');
        $this->assertSessionHas('mfa-setup-totp-secret');
        $svg = $resp->getElementHtml('#main-content .card svg');

        // Validation error, code should remain the same
        $resp = $this->post('/mfa/totp/confirm', [
            'code' => 'abc123',
        ]);
        $resp->assertRedirect('/mfa/totp/generate');
        $resp = $this->followRedirects($resp);
        $resp->assertSee('The provided code is not valid or has expired.');
        $revisitSvg = $resp->getElementHtml('#main-content .card svg');
        $this->assertTrue($svg === $revisitSvg);

        // Successful confirmation
        $google2fa = new Google2FA();
        $secret = decrypt(session()->get('mfa-setup-totp-secret'));
        $otp = $google2fa->getCurrentOtp($secret);
        $resp = $this->post('/mfa/totp/confirm', [
            'code' => $otp,
        ]);
        $resp->assertRedirect('/mfa/setup');

        // Confirmation of setup
        $resp = $this->followRedirects($resp);
        $resp->assertSee('Multi-factor method successfully configured');
        $resp->assertElementContains('a[href$="/mfa/totp/generate"]', 'Reconfigure');

        $this->assertDatabaseHas('mfa_values', [
            'user_id' => $editor->id,
            'method' => 'totp',
        ]);
        $this->assertFalse(session()->has('mfa-setup-totp-secret'));
        $value = MfaValue::query()->where('user_id', '=', $editor->id)
            ->where('method', '=', 'totp')->first();
        $this->assertEquals($secret, decrypt($value->value));
    }

    public function test_backup_codes_setup()
    {
        $editor = $this->getEditor();
        $this->assertDatabaseMissing('mfa_values', ['user_id' => $editor->id]);

        // Setup page state
        $resp = $this->actingAs($editor)->get('/mfa/setup');
        $resp->assertElementContains('a[href$="/mfa/backup_codes/generate"]', 'Setup');

        // Generate page access
        $resp = $this->get('/mfa/backup_codes/generate');
        $resp->assertSee('Backup Codes');
        $resp->assertElementContains('form[action$="/mfa/backup_codes/confirm"]', 'Confirm and Enable');
        $this->assertSessionHas('mfa-setup-backup-codes');
        $codes = decrypt(session()->get('mfa-setup-backup-codes'));
        // Check code format
        $this->assertCount(16, $codes);
        $this->assertEquals(16*11, strlen(implode('', $codes)));
        // Check download link
        $resp->assertSee(base64_encode(implode("\n\n", $codes)));

        // Confirm submit
        $resp = $this->post('/mfa/backup_codes/confirm');
        $resp->assertRedirect('/mfa/setup');

        // Confirmation of setup
        $resp = $this->followRedirects($resp);
        $resp->assertSee('Multi-factor method successfully configured');
        $resp->assertElementContains('a[href$="/mfa/backup_codes/generate"]', 'Reconfigure');

        $this->assertDatabaseHas('mfa_values', [
            'user_id' => $editor->id,
            'method' => 'backup_codes',
        ]);
        $this->assertFalse(session()->has('mfa-setup-backup-codes'));
        $value = MfaValue::query()->where('user_id', '=', $editor->id)
            ->where('method', '=', 'backup_codes')->first();
        $this->assertEquals($codes, json_decode(decrypt($value->value)));
    }

    public function test_backup_codes_cannot_be_confirmed_if_not_previously_generated()
    {
        $resp = $this->asEditor()->post('/mfa/backup_codes/confirm');
        $resp->assertStatus(500);
    }

    public function test_mfa_method_count_is_visible_on_user_edit_page()
    {
        $user = $this->getEditor();
        $resp = $this->actingAs($this->getAdmin())->get($user->getEditUrl());
        $resp->assertSee('0 methods configured');

        MfaValue::upsertWithValue($user, MfaValue::METHOD_TOTP, 'test');
        $resp = $this->get($user->getEditUrl());
        $resp->assertSee('1 method configured');

        MfaValue::upsertWithValue($user, MfaValue::METHOD_BACKUP_CODES, 'test');
        $resp = $this->get($user->getEditUrl());
        $resp->assertSee('2 methods configured');
    }

    public function test_mfa_setup_link_only_shown_when_viewing_own_user_edit_page()
    {
        $admin = $this->getAdmin();
        $resp = $this->actingAs($admin)->get($admin->getEditUrl());
        $resp->assertElementExists('a[href$="/mfa/setup"]');

        $resp = $this->actingAs($admin)->get($this->getEditor()->getEditUrl());
        $resp->assertElementNotExists('a[href$="/mfa/setup"]');
    }

    public function test_mfa_indicator_shows_in_user_list()
    {
        $admin = $this->getAdmin();
        User::query()->where('id', '!=', $admin->id)->delete();

        $resp = $this->actingAs($admin)->get('/settings/users');
        $resp->assertElementNotExists('[title="MFA Configured"] svg');

        MfaValue::upsertWithValue($admin, MfaValue::METHOD_TOTP, 'test');
        $resp = $this->actingAs($admin)->get('/settings/users');
        $resp->assertElementExists('[title="MFA Configured"] svg');
    }

    public function test_remove_mfa_method()
    {
        $admin = $this->getAdmin();

        MfaValue::upsertWithValue($admin, MfaValue::METHOD_TOTP, 'test');
        $this->assertEquals(1, $admin->mfaValues()->count());
        $resp = $this->actingAs($admin)->get('/mfa/setup');
        $resp->assertElementExists('form[action$="/mfa/totp/remove"]');

        $resp = $this->delete("/mfa/totp/remove");
        $resp->assertRedirect("/mfa/setup");
        $resp = $this->followRedirects($resp);
        $resp->assertSee('Multi-factor method successfully removed');

        $this->assertActivityExists(ActivityType::MFA_REMOVE_METHOD);
        $this->assertEquals(0, $admin->mfaValues()->count());
    }

}