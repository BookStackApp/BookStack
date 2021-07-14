<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Http\Controllers\Controller;

class MfaController extends Controller
{
    /**
     * Show the view to setup MFA for the current user.
     */
    public function setup()
    {
        $userMethods = user()->mfaValues()
            ->get(['id', 'method'])
            ->groupBy('method');
        return view('mfa.setup', [
            'userMethods' => $userMethods,
        ]);
    }

    /**
     * Remove an MFA method for the current user.
     * @throws \Exception
     */
    public function remove(string $method)
    {
        if (in_array($method, MfaValue::allMethods())) {
            $value = user()->mfaValues()->where('method', '=', $method)->first();
            if ($value) {
                $value->delete();
                $this->logActivity(ActivityType::MFA_REMOVE_METHOD, $method);
            }
        }

        return redirect('/mfa/setup');
    }
}
