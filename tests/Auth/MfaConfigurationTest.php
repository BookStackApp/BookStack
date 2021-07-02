<?php

namespace Tests\Auth;

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
        $resp->assertElementContains('a[href$="/mfa/totp-generate"]', 'Setup');

        // Generate page access
        $resp = $this->get('/mfa/totp-generate');
        $resp->assertSee('Mobile App Setup');
        $resp->assertSee('Verify Setup');
        $resp->assertElementExists('form[action$="/mfa/totp-confirm"] button');
        $this->assertSessionHas('mfa-setup-totp-secret');
        $svg = $resp->getElementHtml('#main-content .card svg');

        // Validation error, code should remain the same
        $resp = $this->post('/mfa/totp-confirm', [
            'code' => 'abc123',
        ]);
        $resp->assertRedirect('/mfa/totp-generate');
        $resp = $this->followRedirects($resp);
        $resp->assertSee('The provided code is not valid or has expired.');
        $revisitSvg = $resp->getElementHtml('#main-content .card svg');
        $this->assertTrue($svg === $revisitSvg);

        // Successful confirmation
        $google2fa = new Google2FA();
        $otp = $google2fa->getCurrentOtp(decrypt(session()->get('mfa-setup-totp-secret')));
        $resp = $this->post('/mfa/totp-confirm', [
            'code' => $otp,
        ]);
        $resp->assertRedirect('/mfa/setup');

        // Confirmation of setup
        $resp = $this->followRedirects($resp);
        $resp->assertSee('Multi-factor method successfully configured');
        $resp->assertElementContains('a[href$="/mfa/totp-generate"]', 'Reconfigure');
    }

}