<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Access\Mfa\BackupCodeService;
use BookStack\Access\Mfa\MfaSession;
use BookStack\Access\Mfa\MfaValue;
use BookStack\Activity\ActivityType;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MfaBackupCodesController extends Controller
{
    use HandlesPartialLogins;

    protected const SETUP_SECRET_SESSION_KEY = 'mfa-setup-backup-codes';

    /**
     * Show a view that generates and displays backup codes.
     */
    public function generate(BackupCodeService $codeService)
    {
        $codes = $codeService->generateNewSet();
        session()->put(self::SETUP_SECRET_SESSION_KEY, encrypt($codes));

        $downloadUrl = 'data:application/octet-stream;base64,' . base64_encode(implode("\n\n", $codes));

        $this->setPageTitle(trans('auth.mfa_gen_backup_codes_title'));

        return view('mfa.backup-codes-generate', [
            'codes'       => $codes,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    /**
     * Confirm the setup of backup codes, storing them against the user.
     *
     * @throws Exception
     */
    public function confirm()
    {
        if (!session()->has(self::SETUP_SECRET_SESSION_KEY)) {
            return response('No generated codes found in the session', 500);
        }

        $codes = decrypt(session()->pull(self::SETUP_SECRET_SESSION_KEY));
        MfaValue::upsertWithValue($this->currentOrLastAttemptedUser(), MfaValue::METHOD_BACKUP_CODES, json_encode($codes));

        $this->logActivity(ActivityType::MFA_SETUP_METHOD, 'backup-codes');

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
     * @throws ValidationException
     */
    public function verify(Request $request, BackupCodeService $codeService, MfaSession $mfaSession, LoginService $loginService)
    {
        $user = $this->currentOrLastAttemptedUser();
        $codes = MfaValue::getValueForUser($user, MfaValue::METHOD_BACKUP_CODES) ?? '[]';

        $this->validate($request, [
            'code' => [
                'required', 'max:12', 'min:8',
                function ($attribute, $value, $fail) use ($codeService, $codes) {
                    if (!$codeService->inputCodeExistsInSet($value, $codes)) {
                        $fail(trans('validation.backup_codes'));
                    }
                },
            ],
        ]);

        $updatedCodes = $codeService->removeInputCodeFromSet($request->get('code'), $codes);
        MfaValue::upsertWithValue($user, MfaValue::METHOD_BACKUP_CODES, $updatedCodes);

        $mfaSession->markVerifiedForUser($user);
        $loginService->reattemptLoginFor($user);

        if ($codeService->countCodesInSet($updatedCodes) < 5) {
            $this->showWarningNotification(trans('auth.mfa_backup_codes_usage_limit_warning'));
        }

        return redirect()->intended();
    }
}
