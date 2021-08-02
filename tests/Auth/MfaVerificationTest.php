<?php

namespace Tests\Auth;

use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Auth\Access\Mfa\TotpService;
use BookStack\Auth\User;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;
use Tests\TestResponse;

class MfaVerificationTest extends TestCase
{
    public function test_totp_verification()
    {
        [$user, $secret, $loginResp] = $this->startTotpLogin();
        $loginResp->assertRedirect('/mfa/verify');

        $resp = $this->get('/mfa/verify');
        $resp->assertSee('Verify Access');
        $resp->assertSee('Enter the code, generated using your mobile app, below:');
        $resp->assertElementExists('form[action$="/mfa/verify/totp"] input[name="code"]');

        $google2fa = new Google2FA();
        $resp = $this->post('/mfa/verify/totp', [
            'code' => $google2fa->getCurrentOtp($secret),
        ]);
        $resp->assertRedirect('/');
        $this->assertEquals($user->id, auth()->user()->id);
    }

    public function test_totp_verification_fails_on_missing_invalid_code()
    {
        [$user, $secret, $loginResp] = $this->startTotpLogin();

        $resp = $this->get('/mfa/verify');
        $resp = $this->post('/mfa/verify/totp', [
            'code' => '',
        ]);
        $resp->assertRedirect('/mfa/verify');

        $resp = $this->get('/mfa/verify');
        $resp->assertSeeText('The code field is required.');
        $this->assertNull(auth()->user());

        $resp = $this->post('/mfa/verify/totp', [
            'code' => '123321',
        ]);
        $resp->assertRedirect('/mfa/verify');
        $resp = $this->get('/mfa/verify');

        $resp->assertSeeText('The provided code is not valid or has expired.');
        $this->assertNull(auth()->user());
    }

    public function test_backup_code_verification()
    {
        [$user, $codes, $loginResp] = $this->startBackupCodeLogin();
        $loginResp->assertRedirect('/mfa/verify');

        $resp = $this->get('/mfa/verify');
        $resp->assertSee('Verify Access');
        $resp->assertSee('Backup Code');
        $resp->assertSee('Enter one of your remaining backup codes below:');
        $resp->assertElementExists('form[action$="/mfa/verify/backup_codes"] input[name="code"]');

        $resp = $this->post('/mfa/verify/backup_codes', [
            'code' => $codes[1],
        ]);

        $resp->assertRedirect('/');
        $this->assertEquals($user->id, auth()->user()->id);
        // Ensure code no longer exists in available set
        $userCodes = MfaValue::getValueForUser($user, MfaValue::METHOD_BACKUP_CODES);
        $this->assertStringNotContainsString($codes[1], $userCodes);
        $this->assertStringContainsString($codes[0], $userCodes);
    }

    public function test_backup_code_verification_fails_on_missing_or_invalid_code()
    {
        [$user, $codes, $loginResp] = $this->startBackupCodeLogin();

        $resp = $this->get('/mfa/verify');
        $resp = $this->post('/mfa/verify/backup_codes', [
            'code' => '',
        ]);
        $resp->assertRedirect('/mfa/verify');

        $resp = $this->get('/mfa/verify');
        $resp->assertSeeText('The code field is required.');
        $this->assertNull(auth()->user());

        $resp = $this->post('/mfa/verify/backup_codes', [
            'code' => 'ab123-ab456',
        ]);
        $resp->assertRedirect('/mfa/verify');

        $resp = $this->get('/mfa/verify');
        $resp->assertSeeText('The provided code is not valid or has already been used.');
        $this->assertNull(auth()->user());
    }

    public function test_backup_code_verification_fails_on_attempted_code_reuse()
    {
        [$user, $codes, $loginResp] = $this->startBackupCodeLogin();

        $this->post('/mfa/verify/backup_codes', [
            'code' => $codes[0],
        ]);
        $this->assertNotNull(auth()->user());
        auth()->logout();
        session()->flush();

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->get('/mfa/verify');
        $resp = $this->post('/mfa/verify/backup_codes', [
            'code' => $codes[0],
        ]);
        $resp->assertRedirect('/mfa/verify');
        $this->assertNull(auth()->user());

        $resp = $this->get('/mfa/verify');
        $resp->assertSeeText('The provided code is not valid or has already been used.');
    }

    public function test_backup_code_verification_shows_warning_when_limited_codes_remain()
    {
        [$user, $codes, $loginResp] = $this->startBackupCodeLogin(['abc12-def45', 'abc12-def46']);

        $resp = $this->post('/mfa/verify/backup_codes', [
            'code' => $codes[0],
        ]);
        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.');
    }

    public function test_both_mfa_options_available_if_set_on_profile()
    {
        $user = $this->getEditor();
        $user->password = Hash::make('password');
        $user->save();

        MfaValue::upsertWithValue($user, MfaValue::METHOD_TOTP, 'abc123');
        MfaValue::upsertWithValue($user, MfaValue::METHOD_BACKUP_CODES, '["abc12-def456"]');

        /** @var TestResponse $mfaView */
        $mfaView = $this->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Totp shown by default
        $mfaView->assertElementExists('form[action$="/mfa/verify/totp"] input[name="code"]');
        $mfaView->assertElementContains('a[href$="/mfa/verify?method=backup_codes"]', 'Verify using a backup code');

        // Ensure can view backup_codes view
        $resp = $this->get('/mfa/verify?method=backup_codes');
        $resp->assertElementExists('form[action$="/mfa/verify/backup_codes"] input[name="code"]');
        $resp->assertElementContains('a[href$="/mfa/verify?method=totp"]', 'Verify using a mobile app');
    }

    //    TODO !! - Test no-existing MFA

    /**
     * @return Array<User, string, TestResponse>
     */
    protected function startTotpLogin(): array
    {
        $secret = $this->app->make(TotpService::class)->generateSecret();
        $user = $this->getEditor();
        $user->password = Hash::make('password');
        $user->save();
        MfaValue::upsertWithValue($user, MfaValue::METHOD_TOTP, $secret);
        $loginResp = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return [$user, $secret, $loginResp];
    }

    /**
     * @return Array<User, string, TestResponse>
     */
    protected function startBackupCodeLogin($codes = ['kzzu6-1pgll','bzxnf-plygd','bwdsp-ysl51','1vo93-ioy7n','lf7nw-wdyka','xmtrd-oplac']): array
    {
        $user = $this->getEditor();
        $user->password = Hash::make('password');
        $user->save();
        MfaValue::upsertWithValue($user, MfaValue::METHOD_BACKUP_CODES, json_encode($codes));
        $loginResp = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return [$user, $codes, $loginResp];
    }

}