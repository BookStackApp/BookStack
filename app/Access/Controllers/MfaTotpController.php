<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Access\Mfa\MfaSession;
use BookStack\Access\Mfa\MfaValue;
use BookStack\Access\Mfa\TotpService;
use BookStack\Access\Mfa\TotpValidationRule;
use BookStack\Activity\ActivityType;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
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

        $qrCodeUrl = $totp->generateUrl($totpSecret, $this->currentOrLastAttemptedUser());
        $svg = $totp->generateQrCodeSvg($qrCodeUrl);

        $this->setPageTitle(trans('auth.mfa_gen_totp_title'));

        return view('mfa.totp-generate', [
            'url' => $qrCodeUrl,
            'svg' => $svg,
        ]);
    }

    /**
     * Confirm the setup of TOTP and save the auth method secret
     * against the current user.
     *
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
            ],
        ]);

        MfaValue::upsertWithValue($this->currentOrLastAttemptedUser(), MfaValue::METHOD_TOTP, $totpSecret);
        session()->remove(static::SETUP_SECRET_SESSION_KEY);
        $this->logActivity(ActivityType::MFA_SETUP_METHOD, 'totp');

        if (!auth()->check()) {
            $this->showSuccessNotification(trans('auth.mfa_setup_login_notification'));

            return redirect('/login');
        }

        return redirect('/mfa/setup');
    }

    /**
     * Verify the MFA method submission on check.
     *
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
            ],
        ]);

        $mfaSession->markVerifiedForUser($user);
        $loginService->reattemptLoginFor($user);

        return redirect()->intended();
    }
}
