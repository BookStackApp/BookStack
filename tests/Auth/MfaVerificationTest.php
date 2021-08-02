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

}