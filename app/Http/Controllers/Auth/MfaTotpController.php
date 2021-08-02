<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\LoginService;
use BookStack\Auth\Access\Mfa\MfaSession;
use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Auth\Access\Mfa\TotpService;
use BookStack\Auth\Access\Mfa\TotpValidationRule;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MfaTotpController extends Controller
{
    use HandlesPartialLogins;

    protected const SETUP_SECRET_SESSION_KEY = 'mfa-setup-totp-secret';

    /**
     * Show a view that generates and displays a TOTP QR code.
     */
    public function generate(TotpService $totp)
    {
        if (session()->has(static::SETUP_SECRET_SESSION_KEY)) {
            $totpSecret = decrypt(session()->get(static::SETUP_SECRET_SESSION_KEY));
        } else {
            $totpSecret = $totp->generateSecret();
            session()->put(static::SETUP_SECRET_SESSION_KEY, encrypt($totpSecret));
        }

        $qrCodeUrl = $totp->generateUrl($totpSecret);
        $svg = $totp->generateQrCodeSvg($qrCodeUrl);

        return view('mfa.totp-generate', [
            'secret' => $totpSecret,
            'svg' => $svg,
        ]);
    }

    /**
     * Confirm the setup of TOTP and save the auth method secret
     * against the current user.
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function confirm(Request $request)
    {
        $totpSecret = decrypt(session()->get(static::SETUP_SECRET_SESSION_KEY));
        $this->validate($request, [
            'code' => [
                'required',
                'max:12', 'min:4',
                new TotpValidationRule($totpSecret),
            ]
        ]);

        MfaValue::upsertWithValue($this->currentOrLastAttemptedUser(), MfaValue::METHOD_TOTP, $totpSecret);
        session()->remove(static::SETUP_SECRET_SESSION_KEY);
        $this->logActivity(ActivityType::MFA_SETUP_METHOD, 'totp');

        return redirect('/mfa/setup');
    }

    /**
     * Verify the MFA method submission on check.
     * @throws NotFoundException
     */
    public function verify(Request $request, LoginService $loginService, MfaSession $mfaSession)
    {
        $user = $this->currentOrLastAttemptedUser();
        $totpSecret = MfaValue::getValueForUser($user, MfaValue::METHOD_TOTP);

        $this->validate($request, [
            'code' => [
                'required',
                'max:12', 'min:4',
                new TotpValidationRule($totpSecret),
            ]
        ]);

        $mfaSession->markVerifiedForUser($user);
        $loginService->reattemptLoginFor($user);

        return redirect()->intended();
    }
}
