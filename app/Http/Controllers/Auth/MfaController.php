<?php

namespace BookStack\Http\Controllers\Auth;

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
}
