<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\Mfa\BackupCodeService;
use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Http\Controllers\Controller;
use Exception;

class MfaBackupCodesController extends Controller
{
    protected const SETUP_SECRET_SESSION_KEY = 'mfa-setup-backup-codes';

    /**
     * Show a view that generates and displays backup codes
     */
    public function generate(BackupCodeService $codeService)
    {
        $codes = $codeService->generateNewSet();
        session()->put(self::SETUP_SECRET_SESSION_KEY, encrypt($codes));

        $downloadUrl = 'data:application/octet-stream;base64,' . base64_encode(implode("\n\n", $codes));

        return view('mfa.backup-codes-generate', [
            'codes' => $codes,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    /**
     * Confirm the setup of backup codes, storing them against the user.
     * @throws Exception
     */
    public function confirm()
    {
        if (!session()->has(self::SETUP_SECRET_SESSION_KEY)) {
            return response('No generated codes found in the session', 500);
        }

        $codes = decrypt(session()->pull(self::SETUP_SECRET_SESSION_KEY));
        MfaValue::upsertWithValue(user(), MfaValue::METHOD_BACKUP_CODES, json_encode($codes));

        $this->logActivity(ActivityType::MFA_SETUP_METHOD, 'backup-codes');
        return redirect('/mfa/setup');
    }
}
